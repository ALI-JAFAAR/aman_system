<?php

namespace App\Http\Controllers\Api;

use App\Models\LedgerEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class LedgerEntryController extends BaseApiController {

    public function index(Request $request) {
        try {
            $perPage = (int) $request->get('per_page', 20);
            $perPage = $perPage > 0 ? min($perPage, 100) : 20;

            $query = LedgerEntry::query();

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
            return $this->fail('Failed to fetch ledgerEntry list', $e->getMessage(), 500);
        }
    }

    public function show(LedgerEntry $ledgerEntry) {
        try {
            return $this->ok($ledgerEntry);
        } catch (Throwable $e) {
            return $this->fail('Failed to fetch ledgerEntry', $e->getMessage(), 500);
        }
    }

    public function store(Request $request) {
        try {
            $instance = new LedgerEntry();
            $data = $this->filterData($request, $instance);

            return DB::transaction(function() use ($data) {
                $created = LedgerEntry::create($data);
                return $this->ok($created, 'LedgerEntry created', 201);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to create ledgerEntry', $e->getMessage(), 422);
        }
    }

    public function update(Request $request, LedgerEntry $ledgerEntry) {
        try {
            $data = $this->filterData($request, $ledgerEntry);

            return DB::transaction(function() use ($data, $ledgerEntry) {
                $ledgerEntry->update($data);
                return $this->ok($ledgerEntry->fresh(), 'LedgerEntry updated');
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to update ledgerEntry', $e->getMessage(), 422);
        }
    }

    public function destroy(LedgerEntry $ledgerEntry) {
        try {
            return DB::transaction(function() use ($ledgerEntry) {
                $ledgerEntry->delete();
                return $this->ok(null, 'LedgerEntry deleted', 200);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to delete ledgerEntry', $e->getMessage(), 500);
        }
    }
}
