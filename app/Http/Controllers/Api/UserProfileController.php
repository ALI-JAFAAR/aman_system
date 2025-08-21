<?php

namespace App\Http\Controllers\Api;

use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class UserProfileController extends BaseApiController {

    public function index(Request $request) {
        try {
            $perPage = (int) $request->get('per_page', 20);
            $perPage = $perPage > 0 ? min($perPage, 100) : 20;

            $query = UserProfile::query();

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
            return $this->fail('Failed to fetch userProfile list', $e->getMessage(), 500);
        }
    }

    public function show(UserProfile $userProfile) {
        try {
            return $this->ok($userProfile);
        } catch (Throwable $e) {
            return $this->fail('Failed to fetch userProfile', $e->getMessage(), 500);
        }
    }

    public function store(Request $request) {
        try {
            $instance = new UserProfile();
            $data = $this->filterData($request, $instance);

            return DB::transaction(function() use ($data) {
                $created = UserProfile::create($data);
                return $this->ok($created, 'UserProfile created', 201);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to create userProfile', $e->getMessage(), 422);
        }
    }

    public function update(Request $request, UserProfile $userProfile) {
        try {
            $data = $this->filterData($request, $userProfile);

            return DB::transaction(function() use ($data, $userProfile) {
                $userProfile->update($data);
                return $this->ok($userProfile->fresh(), 'UserProfile updated');
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to update userProfile', $e->getMessage(), 422);
        }
    }

    public function destroy(UserProfile $userProfile) {
        try {
            return DB::transaction(function() use ($userProfile) {
                $userProfile->delete();
                return $this->ok(null, 'UserProfile deleted', 200);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to delete userProfile', $e->getMessage(), 500);
        }
    }
}
