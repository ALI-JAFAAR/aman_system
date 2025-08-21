<?php

namespace App\Http\Controllers\Api;

use App\Models\Reconciliation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class ReconciliationController extends BaseApiController {

    public function index(Request $request) {
        try {
            $perPage = (int) $request->get('per_page', 20);
            $perPage = $perPage > 0 ? min($perPage, 100) : 20;

            $query = Reconciliation::query();

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
            return $this->fail('Failed to fetch reconciliation list', $e->getMessage(), 500);
        }
    }

    public function show(Reconciliation $reconciliation) {
        try {
            return $this->ok($reconciliation);
        } catch (Throwable $e) {
            return $this->fail('Failed to fetch reconciliation', $e->getMessage(), 500);
        }
    }

    public function store(Request $request) {
        try {
            $instance = new Reconciliation();
            $data = $this->filterData($request, $instance);

            return DB::transaction(function() use ($data) {
                $created = Reconciliation::create($data);
                return $this->ok($created, 'Reconciliation created', 201);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to create reconciliation', $e->getMessage(), 422);
        }
    }

    public function update(Request $request, Reconciliation $reconciliation) {
        try {
            $data = $this->filterData($request, $reconciliation);

            return DB::transaction(function() use ($data, $reconciliation) {
                $reconciliation->update($data);
                return $this->ok($reconciliation->fresh(), 'Reconciliation updated');
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to update reconciliation', $e->getMessage(), 422);
        }
    }

    public function destroy(Reconciliation $reconciliation) {
        try {
            return DB::transaction(function() use ($reconciliation) {
                $reconciliation->delete();
                return $this->ok(null, 'Reconciliation deleted', 200);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to delete reconciliation', $e->getMessage(), 500);
        }
    }
}
