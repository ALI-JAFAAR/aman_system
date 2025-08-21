<?php

namespace App\Http\Controllers\Api;

use App\Models\WithdrawRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class WithdrawRequestController extends BaseApiController {

    public function index(Request $request) {
        try {
            $perPage = (int) $request->get('per_page', 20);
            $perPage = $perPage > 0 ? min($perPage, 100) : 20;

            $query = WithdrawRequest::query();

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
            return $this->fail('Failed to fetch withdrawRequest list', $e->getMessage(), 500);
        }
    }

    public function show(WithdrawRequest $withdrawRequest) {
        try {
            return $this->ok($withdrawRequest);
        } catch (Throwable $e) {
            return $this->fail('Failed to fetch withdrawRequest', $e->getMessage(), 500);
        }
    }

    public function store(Request $request) {
        try {
            $instance = new WithdrawRequest();
            $data = $this->filterData($request, $instance);

            return DB::transaction(function() use ($data) {
                $created = WithdrawRequest::create($data);
                return $this->ok($created, 'WithdrawRequest created', 201);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to create withdrawRequest', $e->getMessage(), 422);
        }
    }

    public function update(Request $request, WithdrawRequest $withdrawRequest) {
        try {
            $data = $this->filterData($request, $withdrawRequest);

            return DB::transaction(function() use ($data, $withdrawRequest) {
                $withdrawRequest->update($data);
                return $this->ok($withdrawRequest->fresh(), 'WithdrawRequest updated');
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to update withdrawRequest', $e->getMessage(), 422);
        }
    }

    public function destroy(WithdrawRequest $withdrawRequest) {
        try {
            return DB::transaction(function() use ($withdrawRequest) {
                $withdrawRequest->delete();
                return $this->ok(null, 'WithdrawRequest deleted', 200);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to delete withdrawRequest', $e->getMessage(), 500);
        }
    }
}
