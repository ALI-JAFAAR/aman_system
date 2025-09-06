<?php
// app/Services/ReconciliationBuilder.php

namespace App\Services;

use App\Models\{
    Reconciliation,
    ReconciliationEntry,
    LedgerEntry,
    Contract,
    Employee
};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class ReconciliationBuilder{
    /**
     * بناء تسوية جديدة وربط قيود الفترة بها.
     */
    public function build(string $kind, int $organizationId, string $from, string $to): Reconciliation{
        return DB::transaction(function () use ($kind, $organizationId, $from, $to) {
            // 1) العقد الفعّال تلقائيًا (contract_end > اليوم)
            $activeContract = Contract::activeForOrganization($organizationId);
            if (! $activeContract) {
                throw ValidationException::withMessages([
                    'organization_id' => 'لا يوجد عقد فعّال لهذه الجهة (contract_end يجب أن يكون أكبر من اليوم).',
                ]);
            }

            // 2) موظف المنصّة الحالي (قد يكون null)
            $employeeId = Employee::where('user_id', Auth::id())->value('id');

            // 3) المجاميع (اعتمد على كلاس المجاميع لديك)
            $totals = $kind === 'partner'
                ? ReconciliationTotals::forPartner($organizationId, $from, $to)
                : ReconciliationTotals::forHost($organizationId, $from, $to);

            // 4) رأس التسوية
            $rec = Reconciliation::create([
                'organization_id'          => $organizationId,
                'contract_id'              => $activeContract->id,
                'period_start'             => $from,
                'period_end'               => $to,
                'total_gross_amount'       => $totals['total_gross_amount']       ?? 0,
                'total_platform_share'     => $totals['total_platform_share']     ?? 0,
                'total_organization_share' => $totals['total_organization_share'] ?? 0,
                'total_partner_share'      => $totals['total_partner_share']      ?? 0,
                'status'                   => 'draft',
                'platform_reconciled_at'   => null,
                'platform_reconciled_by'   => $employeeId,   // اجعل العمود قابلًا لـ NULL في الداتابيس
                'partner_reconciled_by'    => null,
            ]);

            // 5) ربط القيود غير المقفلة ضمن الفترة
            $entries = $this->entriesQuery($kind, $organizationId, $from, $to)->get();

            foreach ($entries as $le) {
                ReconciliationEntry::create([
                    'reconciliation_id' => $rec->id,
                    'ledger_entry_id'   => $le->id,
                ]);
            }

            return $rec->fresh();
        });
    }

    /**
     * تمييز موافقة المنصّة على التسوية.
     */
    public function markPlatformReconciled(Reconciliation $rec, ?int $employeeId = null): Reconciliation{
        return DB::transaction(function () use ($rec, $employeeId) {
            $rec->update([
                'platform_reconciled_at' => Carbon::now(),
                'platform_reconciled_by' => $employeeId,
                'status'                 => 'pending_partner',
            ]);

            // إذا كان الشريك قد اعتمد مسبقًا، اقفلها مباشرة
            if (! is_null($rec->partner_reconciled_by)) {
                return $this->close($rec, true);
            }

            return $rec->fresh();
        });
    }

    /**
     * تمييز اعتماد الشريك (شركة التأمين/الجهة) للتسوية.
     */
    public function markPartnerReconciled(Reconciliation $rec, ?int $employeeId = null): Reconciliation{
        return DB::transaction(function () use ($rec, $employeeId) {
            $rec->update([
                'partner_reconciled_by' => $employeeId,
                // لا نغيّر الحالة للـ closed إلا إذا المنصّة اعتمدت أيضًا
                'status' => $rec->platform_reconciled_at ? 'partner_reconciled' : 'partner_reconciled',
            ]);

            // إذا كانت المنصّة اعتمدت مسبقًا، اقفلها مباشرة
            if (! is_null($rec->platform_reconciled_at)) {
                return $this->close($rec, true);
            }

            return $rec->fresh();
        });
    }

    /**
     * إقفال التسوية (وقفل القيود المرتبطة).
     */
    public function close(Reconciliation $rec, bool $lockEntries = true): Reconciliation{
        return DB::transaction(function () use ($rec, $lockEntries) {
            // اقفل القيود المرتبطة بهذه التسوية
            if ($lockEntries) {
                $ids = $rec->reconciliationEntries()->pluck('ledger_entry_id');
                if ($ids->isNotEmpty()) {
                    LedgerEntry::whereIn('id', $ids)->update(['is_locked' => true]);
                }
            }

            $rec->update(['status' => 'confirmed']);

            return $rec->fresh();
        });
    }

    /**
     * Alias اختياري — لتمرير الاستدعاءات القديمة finalize() إلى close().
     */
    public function finalize(Reconciliation $rec, bool $lockEntries = true): Reconciliation{
        return $this->close($rec, $lockEntries);
    }

    /**
     * الاستعلام عن القيود التي ستدخل التسوية (غير مقفلة وضمن الفترة).
     */
    protected function entriesQuery(string $kind, int $organizationId, string $from, string $to){
        // NOTE: استخدم ثوابت الحسابات لديك هنا (ACC_PAY_HOST, ACC_PAY_PARTNER)
        return LedgerEntry::query()
            ->whereBetween(DB::raw('DATE(posted_at)'), [$from, $to])
            ->where('is_locked', false)
            ->when($kind === 'host', function ($q) use ($organizationId) {
                $q->where('account_code', \App\Services\AffiliationPostingService::ACC_PAY_HOST)
                    ->where('organization_id', $organizationId);
            })
            ->when($kind === 'partner', function ($q) use ($organizationId) {
                $q->where('account_code', \App\Services\AffiliationPostingService::ACC_PAY_PARTNER)
                    ->where('organization_id', $organizationId);
            });
    }

}
