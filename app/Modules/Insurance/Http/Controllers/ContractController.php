<?php

namespace App\Modules\Insurance\Http\Controllers;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Contract;
use App\Modules\Insurance\Http\Requests\StoreContractRequest;
use App\Modules\Insurance\Http\Requests\UpdateContractRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class ContractController extends BaseApiController
{
    public function index(Request $request)
    {
        try {
            $perPage = (int) $request->integer('per_page', 20);
            $perPage = $perPage > 0 ? min($perPage, 100) : 20;

            $query = Contract::query()
                ->with(['organization', 'partnerOffering.organization', 'partnerOffering.package'])
                ->latest('id');

            if ($orgId = $request->get('organization_id')) {
                $query->where('organization_id', (int) $orgId);
            }
            if ($initiator = $request->get('initiator_type')) {
                $query->where('initiator_type', (string) $initiator);
            }
            if ($serviceType = $request->get('service_type')) {
                $query->where('service_type', (string) $serviceType);
            }

            return $this->ok($query->paginate($perPage));
        } catch (Throwable $e) {
            return $this->fail('Failed to fetch contracts', $e->getMessage(), 500);
        }
    }

    public function show(Contract $contract)
    {
        $contract->loadMissing(['organization', 'partnerOffering.organization', 'partnerOffering.package']);
        return $this->ok($contract);
    }

    public function store(StoreContractRequest $request)
    {
        try {
            return DB::transaction(function () use ($request) {
                $contract = Contract::create($request->validated());
                return $this->ok($contract->fresh(['organization', 'partnerOffering']), 'Contract created', 201);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to create contract', $e->getMessage(), 422);
        }
    }

    public function update(UpdateContractRequest $request, Contract $contract)
    {
        try {
            return DB::transaction(function () use ($request, $contract) {
                $contract->update($request->validated());
                return $this->ok($contract->fresh(['organization', 'partnerOffering']), 'Contract updated');
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to update contract', $e->getMessage(), 422);
        }
    }

    public function destroy(Contract $contract)
    {
        try {
            return DB::transaction(function () use ($contract) {
                $contract->delete();
                return $this->ok(null, 'Contract deleted');
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to delete contract', $e->getMessage(), 500);
        }
    }
}

