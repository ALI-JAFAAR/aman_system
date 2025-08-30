<?php
// App/Services/ReconciliationBuilder.php

namespace App\Services;

use App\Models\{
    Reconciliation, ReconciliationEntry, LedgerEntry,
    Contract, Employee
};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReconciliationBuilder
{
    /**
     * $kind: 'host' لتسوية الجهة المضيفة، أو 'partner' لتسوية شركة التأمين.
     */
    public function build(string $kind, int $organizationId, string $from, string $to): Reconciliation
    {
        return DB::transaction(function () use ($kind, $organizationId, $from, $to) {

            // 1) اختر العقد الفعّال تلقائيًا
            $activeContract = Contract::activeForOrganization($organizationId);
            if (! $activeContract) {
                throw ValidationException::withMessages([
                    'organization_id' => 'لا يوجد عقد فعّال لهذه الجهة (contract_end يجب أن يكون أكبر من اليوم).',
                ]);
            }

            // 2) موظف المنصّة الحالي
            $employeeId = Employee::where('user_id', Auth::id())->value('id'); // قد يكون null إذا لم تربط الموظفين بالمستخدمين

            // 3) احسب المجاميع بطريقتك الحالية
            $totals = $kind === 'partner'
                ? ReconciliationTotals::forPartner($organizationId, $from, $to)
                : ReconciliationTotals::forHost($organizationId, $from, $to);

            // 4) أنشئ رأس التسوية
            $rec = Reconciliation::create([
                'organization_id'          => $organizationId,
                'contract_id'              => $activeContract->id,     // تلقائي
                'period_start'             => $from,
                'period_end'               => $to,
                'total_gross_amount'       => $totals['total_gross_amount']       ?? 0,
                'total_platform_share'     => $totals['total_platform_share']     ?? 0,
                'total_organization_share' => $totals['total_organization_share'] ?? 0,
                'total_partner_share'      => $totals['total_partner_share']      ?? 0,
                'status'                   => 'draft',
                'platform_reconciled_at'   => null,
                'platform_reconciled_by'   => $employeeId,  // يفضّل أن يكون nullable في الداتابيس
                'partner_reconciled_by'    => null,
            ]);

            // 5) اجلب القيود غير المُسوَّاة ضمن الفترة واربطها (نفس منطقك الحالي)
            $entries = $this->entriesQuery($kind, $organizationId, $from, $to)->get();

            foreach ($entries as $le) {
                ReconciliationEntry::create([
                    'reconciliation_id' => $rec->id,
                    'ledger_entry_id'   => $le->id,
                ]);
            }

            return $rec;
        });
    }

    /**
     * مثال لاستعلام القيود حسب النوع.
     */
    protected function entriesQuery(string $kind, int $organizationId, string $from, string $to)
    {
        // عدّل هذا ليستثني القيود المقفلة/المسواة بالفعل
        return LedgerEntry::query()
            ->whereBetween(DB::raw('DATE(posted_at)'), [$from, $to])
            ->where('is_locked', false)
            ->when($kind === 'host', function ($q) use ($organizationId) {
                // قيود مستحقات الجهة المضيفة
                $q->where('account_code', AffiliationPostingService::ACC_PAY_HOST)
                    ->where('organization_id', $organizationId);
            })
            ->when($kind === 'partner', function ($q) use ($organizationId) {
                // قيود مستحقات الشريك (شركة التأمين)
                $q->where('account_code', AffiliationPostingService::ACC_PAY_PARTNER)
                    ->where('organization_id', $organizationId);
            });
    }
}
