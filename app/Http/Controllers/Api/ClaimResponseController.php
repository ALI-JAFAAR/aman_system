<?php

namespace App\Http\Controllers\Api;

use App\Models\ClaimResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class ClaimResponseController extends BaseApiController {

    public function index(Request $request) {
        try {
            $perPage = (int) $request->get('per_page', 20);
            $perPage = $perPage > 0 ? min($perPage, 100) : 20;

            $query = ClaimResponse::query();

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
            return $this->fail('Failed to fetch claimResponse list', $e->getMessage(), 500);
        }
    }

    public function show(ClaimResponse $claimResponse) {
        try {
            return $this->ok($claimResponse);
        } catch (Throwable $e) {
            return $this->fail('Failed to fetch claimResponse', $e->getMessage(), 500);
        }
    }

    public function store(Request $request) {
        try {
            $instance = new ClaimResponse();
            $data = $this->filterData($request, $instance);

            return DB::transaction(function() use ($data) {
                $created = ClaimResponse::create($data);
                return $this->ok($created, 'ClaimResponse created', 201);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to create claimResponse', $e->getMessage(), 422);
        }
    }

    public function update(Request $request, ClaimResponse $claimResponse) {
        try {
            $data = $this->filterData($request, $claimResponse);

            return DB::transaction(function() use ($data, $claimResponse) {
                $claimResponse->update($data);
                return $this->ok($claimResponse->fresh(), 'ClaimResponse updated');
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to update claimResponse', $e->getMessage(), 422);
        }
    }

    public function destroy(ClaimResponse $claimResponse) {
        try {
            return DB::transaction(function() use ($claimResponse) {
                $claimResponse->delete();
                return $this->ok(null, 'ClaimResponse deleted', 200);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to delete claimResponse', $e->getMessage(), 500);
        }
    }
}
