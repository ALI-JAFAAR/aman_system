<?php

namespace App\Modules\Accounting\Http\Controllers;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Reconciliation;
use App\Services\ReconciliationBuilder;
use Illuminate\Http\Request;
use Throwable;

class ReconciliationController extends BaseApiController
{
    public function index(Request $request)
    {
        try {
            $perPage = (int) $request->integer('per_page', 20);
            $perPage = $perPage > 0 ? min($perPage, 100) : 20;

            $query = Reconciliation::query()
                ->with(['organization', 'contract'])
                ->latest('id');

            if ($orgId = $request->get('organization_id')) {
                $query->where('organization_id', (int) $orgId);
            }
            if ($status = $request->get('status')) {
                $query->where('status', (string) $status);
            }

            return $this->ok($query->paginate($perPage));
        } catch (Throwable $e) {
            return $this->fail('Failed to fetch reconciliations', $e->getMessage(), 500);
        }
    }

    public function show(Reconciliation $reconciliation)
    {
        $reconciliation->loadMissing([
            'organization',
            'contract',
            'reconciliationEntries.ledgerEntry.invoice',
        ]);

        return $this->ok($reconciliation);
    }

    public function store(Request $request, ReconciliationBuilder $builder)
    {
        $data = $request->validate([
            'kind' => ['required', 'in:partner,host'],
            'organization_id' => ['required', 'integer', 'exists:organizations,id'],
            'period_start' => ['required', 'date'],
            'period_end' => ['required', 'date', 'after_or_equal:period_start'],
        ]);

        try {
            $rec = $builder->build(
                kind: (string) $data['kind'],
                organizationId: (int) $data['organization_id'],
                from: (string) $data['period_start'],
                to: (string) $data['period_end'],
            );

            return $this->ok($rec, 'Reconciliation created', 201);
        } catch (Throwable $e) {
            return $this->fail('Failed to create reconciliation', $e->getMessage(), 422);
        }
    }

    public function platformOk(Reconciliation $reconciliation, ReconciliationBuilder $builder)
    {
        $rec = $builder->markPlatformReconciled($reconciliation, optional(auth()->user()?->employee)->id ?? null);
        return $this->ok($rec, 'Platform reconciled');
    }

    public function partnerOk(Reconciliation $reconciliation, ReconciliationBuilder $builder)
    {
        $rec = $builder->markPartnerReconciled($reconciliation, optional(auth()->user()?->employee)->id ?? null);
        return $this->ok($rec, 'Partner reconciled');
    }

    public function close(Reconciliation $reconciliation, ReconciliationBuilder $builder)
    {
        $rec = $builder->close($reconciliation, true);
        return $this->ok($rec, 'Reconciliation closed');
    }
}

