<?php

namespace App\Http\Controllers\Api;

use App\Models\UserAffiliation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class UserAffiliationController extends BaseApiController {

    public function index(Request $request) {
        try {
            $perPage = (int) $request->get('per_page', 20);
            $perPage = $perPage > 0 ? min($perPage, 100) : 20;

            $query = UserAffiliation::query();

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
            return $this->fail('Failed to fetch userAffiliation list', $e->getMessage(), 500);
        }
    }

    public function show(UserAffiliation $userAffiliation) {
        try {
            return $this->ok($userAffiliation);
        } catch (Throwable $e) {
            return $this->fail('Failed to fetch userAffiliation', $e->getMessage(), 500);
        }
    }

    public function store(Request $request) {
        try {
            $instance = new UserAffiliation();
            $data = $this->filterData($request, $instance);

            return DB::transaction(function() use ($data) {
                $created = UserAffiliation::create($data);
                return $this->ok($created, 'UserAffiliation created', 201);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to create userAffiliation', $e->getMessage(), 422);
        }
    }

    public function update(Request $request, UserAffiliation $userAffiliation) {
        try {
            $data = $this->filterData($request, $userAffiliation);

            return DB::transaction(function() use ($data, $userAffiliation) {
                $userAffiliation->update($data);
                return $this->ok($userAffiliation->fresh(), 'UserAffiliation updated');
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to update userAffiliation', $e->getMessage(), 422);
        }
    }

    public function destroy(UserAffiliation $userAffiliation) {
        try {
            return DB::transaction(function() use ($userAffiliation) {
                $userAffiliation->delete();
                return $this->ok(null, 'UserAffiliation deleted', 200);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to delete userAffiliation', $e->getMessage(), 500);
        }
    }
}
