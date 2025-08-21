<?php

namespace App\Http\Controllers\Api;

use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class ContractController extends BaseApiController {

    public function index(Request $request) {
        try {
            $perPage = (int) $request->get('per_page', 20);
            $perPage = $perPage > 0 ? min($perPage, 100) : 20;

            $query = Contract::query();

            // Optionally add simple filters: ?q=search
            if ($search = $request->get('q')) {
                // naive search over 'id' and timestamp columns; customize later
                $query->where(function($q) use ($search) {
                    $q->where('id', 'like', "%{$search}%");
                });
            }

            $data = $query->paginate($perPage);
            return $this->ok($data);
        } catch (Throwable $e) {
            return $this->fail('Failed to fetch contract list', $e->getMessage(), 500);
        }
    }

    public function show(Contract $contract) {
        try {
            return $this->ok($contract);
        } catch (Throwable $e) {
            return $this->fail('Failed to fetch contract', $e->getMessage(), 500);
        }
    }

    public function store(Request $request) {
        try {
            $instance = new Contract();
            $data = $this->filterData($request, $instance);

            return DB::transaction(function() use ($data) {
                $created = Contract::create($data);
                return $this->ok($created, 'Contract created', 201);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to create contract', $e->getMessage(), 422);
        }
    }

    public function update(Request $request, Contract $contract) {
        try {
            $data = $this->filterData($request, $contract);

            return DB::transaction(function() use ($data, $contract) {
                $contract->update($data);
                return $this->ok($contract->fresh(), 'Contract updated');
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to update contract', $e->getMessage(), 422);
        }
    }

    public function destroy(Contract $contract) {
        try {
            return DB::transaction(function() use ($contract) {
                $contract->delete();
                return $this->ok(null, 'Contract deleted', 200);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to delete contract', $e->getMessage(), 500);
        }
    }
}
