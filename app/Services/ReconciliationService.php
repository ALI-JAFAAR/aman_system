<?php

namespace App\Services;

use App\Models\InvoiceItem;
use App\Models\LedgerEntry;
use App\Models\Reconciliation;
use App\Models\ReconciliationEntry;
use App\Models\UserOffering;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ReconciliationService{
    // من AffiliationPostingService
    public const ACC_PAY_PARTNER = '2100';  // مستحقات الشريك
    public const ACC_PAY_HOST = '2200';  // مستحقات الجهة

    /**
     * $type: 'partner' لشركة التأمين (2100) — أو 'host' للجهة المضيفة (2200).
     * يعيد: [rows => Collection<LedgerEntry>, totals => array]
     */
    public function preview(int $organizationId, string $type, string $fromDate, string $toDate, ?int $contractId = null): array{
        [$from, $to] = [Carbon::parse($fromDate)->startOfDay(), Carbon::parse($toDate)->endOfDay()];

        // ❶ القيود المرشّحة (غير المضمنة سابقًا بأي تسوية)
        $ledgerQuery = LedgerEntry::query()
            ->whereBetween('posted_at', [$from, $to])
            ->whereNull('deleted_at')
            ->leftJoin('reconciliation_entries as re', 're.ledger_entry_id', '=', 'ledger_entries.id')
            ->whereNull('re.id');

        if ($type === 'partner') {
            // نربط لمعرفة أن هذا السطر يخص شريك التأمين المحدّد
            $ledgerQuery
                ->join('user_offerings as uo', function ($j) {
                    $j
                        ->on('ledger_entries.reference_id', '=', 'uo.id')
                        ->where('ledger_entries.reference_type', '=', UserOffering::class);
                })
                ->join('partner_offerings as po', 'po.id', '=', 'uo.partner_offering_id')
                ->where('po.organization_id', $organizationId)
                ->where('ledger_entries.account_code', self::ACC_PAY_PARTNER);

            if ($contractId) {
                $ledgerQuery->where('po.contract_id', $contractId);
            }
        } else {  // host
            // نستدل على الجهة من بنود الفواتير (offering) المرتبطة بفاتورة هذا القيد
            $ledgerQuery
                ->join('invoice_items as ii', 'ii.invoice_id', '=', 'ledger_entries.invoice_id')
                ->where('ii.item_type', 'offering')
                ->where('ii.organization_id', $organizationId)
                ->where('ledger_entries.account_code', self::ACC_PAY_HOST);

            if ($contractId) {
                // إن كانت لديك علاقة العقد على PartnerOffering، يمكن الربط عبر user_offerings أيضًا، وإلا تجاهل
                $ledgerQuery
                    ->leftJoin('user_offerings as uo2', function ($j) {
                        $j
                            ->on('ledger_entries.reference_id', '=', 'uo2.id')
                            ->where('ledger_entries.reference_type', '=', UserOffering::class);
                    })
                    ->leftJoin('partner_offerings as po2', 'po2.id', '=', 'uo2.partner_offering_id')
                    ->where('po2.contract_id', $contractId);
            }
        }

        $rows = $ledgerQuery
            ->orderBy('posted_at')
            ->orderBy('ledger_entries.id')
            ->select('ledger_entries.*')
            ->get();

        // ❷ الإجماليات من invoice_items (أدق من ledger لتجميع الحصص)
        $items = InvoiceItem::query()
            ->whereBetween('invoice_items.created_at', [$from, $to])
            ->where('invoice_items.item_type', 'offering');

        if ($type === 'partner') {
            $items
                ->join('user_offerings as uo', function ($j) {
                    $j
                        ->on('invoice_items.reference_id', '=', 'uo.id')
                        ->where('invoice_items.reference_type', '=', UserOffering::class);
                })
                ->join('partner_offerings as po', 'po.id', '=', 'uo.partner_offering_id')
                ->where('po.organization_id', $organizationId);

            if ($contractId) {
                $items->where('po.contract_id', $contractId);
            }
        } else {
            $items->where('invoice_items.organization_id', $organizationId);
            // عقد الجهة المضيفة لو كان عندك عمود contract_id على invoice_items فاستعمله هنا
        }

        $totals = [
            'total_gross_amount' => (float) $items->sum('invoice_items.line_total'),
            'total_platform_share' => (float) InvoiceItem::from('invoice_items')->whereKey($items->pluck('invoice_items.id'))->sum('platform_share'),
            'total_organization_share' => (float) InvoiceItem::from('invoice_items')->whereKey($items->pluck('invoice_items.id'))->sum('host_share'),
            'total_partner_share' => (float) InvoiceItem::from('invoice_items')->whereKey($items->pluck('invoice_items.id'))->sum('partner_share'),
        ];

        return compact('rows', 'totals');
    }

    /**
     * ينشئ تسوية + يُرفق القيود + يملأ الإجماليات من preview().
     * status يبدأ "draft".
     */
    public function create(int $organizationId, string $type, string $fromDate, string $toDate, ?int $contractId, int $platformEmployeeId): Reconciliation{
        $data = $this->preview($organizationId, $type, $fromDate, $toDate, $contractId);

        return DB::transaction(function () use ($organizationId, $contractId, $fromDate, $toDate, $platformEmployeeId, $data) {
            $rec = Reconciliation::create([
                'organization_id' => $organizationId,
                'contract_id' => $contractId,
                'period_start' => $fromDate,
                'period_end' => $toDate,
                'total_gross_amount' => $data['totals']['total_gross_amount'] ?? 0,
                'total_platform_share' => $data['totals']['total_platform_share'] ?? 0,
                'total_organization_share' => $data['totals']['total_organization_share'] ?? 0,
                'total_partner_share' => $data['totals']['total_partner_share'] ?? 0,
                'status' => 'draft',
                'platform_reconciled_at' => now(),
                'platform_reconciled_by' => $platformEmployeeId,
            ]);

            // اربط كل سطر LedgerEntry بهذه التسوية
            $rows = $data['rows'];
            if ($rows->isNotEmpty()) {
                $insert = $rows->map(fn($e) => [
                    'reconciliation_id' => $rec->id,
                    'ledger_entry_id' => $e->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])->all();

                ReconciliationEntry::insert($insert);
            }

            return $rec;
        });
    }

    /**
     * اعتماد الشريك (يغيّر الحالة إن أردت)
     */
    public function partnerApprove(Reconciliation $rec, int $partnerEmployeeId): void{
        $rec->update([
            'partner_reconciled_by' => $partnerEmployeeId,
            'status' => 'partner_approved',
        ]);
    }

    /**
     * إقفال التسوية + قفل القيود المضمّنة اختيارياً
     */
    public function close(Reconciliation $rec, bool $lockLedger = true): void{
        DB::transaction(function () use ($rec, $lockLedger) {
            if ($lockLedger) {
                LedgerEntry::whereIn(
                    'id',
                    ReconciliationEntry::where('reconciliation_id', $rec->id)->pluck('ledger_entry_id')
                )->update(['is_locked' => true]);
            }

            $rec->update(['status' => 'closed']);
        });
    }
}
