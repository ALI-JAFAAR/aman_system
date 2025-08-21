<?php

namespace App\Http\Controllers\Api;

use App\Models\ReconciliationEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class ReconciliationEntryController extends BaseApiController {

    public function index(Request $request) {
        try {
            $perPage = (int) $request->get('per_page', 20);
            $perPage = $perPage > 0 ? min($perPage, 100) : 20;

            $query = ReconciliationEntry::query();

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
            return $this->fail('Failed to fetch reconciliationEntry list', $e->getMessage(), 500);
        }
    }

    public function show(ReconciliationEntry $reconciliationEntry) {
        try {
            return $this->ok($reconciliationEntry);
        } catch (Throwable $e) {
            return $this->fail('Failed to fetch reconciliationEntry', $e->getMessage(), 500);
        }
    }

    public function store(Request $request) {
        try {
            $instance = new ReconciliationEntry();
            $data = $this->filterData($request, $instance);

            return DB::transaction(function() use ($data) {
                $created = ReconciliationEntry::create($data);
                return $this->ok($created, 'ReconciliationEntry created', 201);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to create reconciliationEntry', $e->getMessage(), 422);
        }
    }

    public function update(Request $request, ReconciliationEntry $reconciliationEntry) {
        try {
            $data = $this->filterData($request, $reconciliationEntry);

            return DB::transaction(function() use ($data, $reconciliationEntry) {
                $reconciliationEntry->update($data);
                return $this->ok($reconciliationEntry->fresh(), 'ReconciliationEntry updated');
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to update reconciliationEntry', $e->getMessage(), 422);
        }
    }

    public function destroy(ReconciliationEntry $reconciliationEntry) {
        try {
            return DB::transaction(function() use ($reconciliationEntry) {
                $reconciliationEntry->delete();
                return $this->ok(null, 'ReconciliationEntry deleted', 200);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to delete reconciliationEntry', $e->getMessage(), 500);
        }
    }
}
