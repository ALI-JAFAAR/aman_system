<?php
namespace App\Services;

use App\Models\{Reconciliation, ReconciliationEntry, LedgerEntry};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReconciliationBuilder
{
    /**
     * $kind: 'partner' or 'host'
     */
    public function build(string $kind, int $organizationId, string $from, string $to, ?int $contractId = null): Reconciliation
    {
        return DB::transaction(function () use ($kind, $organizationId, $from, $to, $contractId) {

            // 1) Compute totals based on kind
            $totals = $kind === 'partner'
                ? ReconciliationTotals::forPartner($organizationId, $from, $to)
                : ReconciliationTotals::forHost($organizationId, $from, $to);

            // 2) Create header
            $rec = Reconciliation::create([
                'organization_id'           => $organizationId,
                'contract_id'               => $contractId,
                'period_start'              => $from,
                'period_end'                => $to,
                'total_gross_amount'        => $totals['total_gross_amount'] ?? 0,
                'total_platform_share'      => $totals['total_platform_share'] ?? 0,
                'total_organization_share'  => $totals['total_organization_share'] ?? 0,
                'total_partner_share'       => $totals['total_partner_share'] ?? 0,
                'status'                    => 'draft',
                'platform_reconciled_at'    => null,
                'platform_reconciled_by'    => null,
                'partner_reconciled_by'     => null,
            ]);

            // 3) Attach candidate ledger entries (not already reconciled)
            $base = LedgerEntry::query()
                ->when($kind === 'partner',
                    fn($q) => $q->forPartnerOrg($organizationId),
                    fn($q) => $q->forHostOrg($organizationId)
                )
                ->whereBetween('posted_at', [$from.' 00:00:00', $to.' 23:59:59'])
                ->unreconciled()
                ->orderBy('posted_at')
                ->get(['ledger_entries.id']);

            foreach ($base as $e) {
                ReconciliationEntry::create([
                    'reconciliation_id' => $rec->id,
                    'ledger_entry_id'   => $e->id,
                ]);
            }

            return $rec->load(['organization','reconciliationEntries.ledgerEntry']);
        });
    }

    public function markPlatformReconciled(Reconciliation $rec, int $employeeId): void
    {
        $rec->update([
            'status'                 => 'platform_reconciled',
            'platform_reconciled_at' => now(),
            'platform_reconciled_by' => $employeeId,
        ]);
    }

    public function markPartnerReconciled(Reconciliation $rec, int $employeeId): void
    {
        $rec->update([
            'status'               => 'partner_reconciled',
            'partner_reconciled_by'=> $employeeId,
        ]);
    }

    public function close(Reconciliation $rec, bool $lockEntries = true): void
    {
        $rec->update(['status' => 'closed']);

        if ($lockEntries) {
            \App\Models\LedgerEntry::whereIn(
                'id',
                $rec->reconciliationEntries()->pluck('ledger_entry_id')
            )->update(['is_locked' => true]);
        }
    }
}
