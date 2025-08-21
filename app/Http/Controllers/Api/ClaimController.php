<?php

namespace App\Http\Controllers\Api;

use App\Models\Claim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class ClaimController extends BaseApiController {

    public function index(Request $request) {
        try {
            $perPage = (int) $request->get('per_page', 20);
            $perPage = $perPage > 0 ? min($perPage, 100) : 20;

            $query = Claim::query();

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
            return $this->fail('Failed to fetch claim list', $e->getMessage(), 500);
        }
    }

    public function show(Claim $claim) {
        try {
            return $this->ok($claim);
        } catch (Throwable $e) {
            return $this->fail('Failed to fetch claim', $e->getMessage(), 500);
        }
    }

    public function store(Request $request) {
        try {
            $instance = new Claim();
            $data = $this->filterData($request, $instance);

            return DB::transaction(function() use ($data) {
                $created = Claim::create($data);
                return $this->ok($created, 'Claim created', 201);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to create claim', $e->getMessage(), 422);
        }
    }

    public function update(Request $request, Claim $claim) {
        try {
            $data = $this->filterData($request, $claim);

            return DB::transaction(function() use ($data, $claim) {
                $claim->update($data);
                return $this->ok($claim->fresh(), 'Claim updated');
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to update claim', $e->getMessage(), 422);
        }
    }

    public function destroy(Claim $claim) {
        try {
            return DB::transaction(function() use ($claim) {
                $claim->delete();
                return $this->ok(null, 'Claim deleted', 200);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to delete claim', $e->getMessage(), 500);
        }
    }
}
