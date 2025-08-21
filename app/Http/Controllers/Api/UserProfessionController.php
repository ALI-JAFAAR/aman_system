<?php

namespace App\Http\Controllers\Api;

use App\Models\UserProfession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class UserProfessionController extends BaseApiController {

    public function index(Request $request) {
        try {
            $perPage = (int) $request->get('per_page', 20);
            $perPage = $perPage > 0 ? min($perPage, 100) : 20;

            $query = UserProfession::query();

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
            return $this->fail('Failed to fetch userProfession list', $e->getMessage(), 500);
        }
    }

    public function show(UserProfession $userProfession) {
        try {
            return $this->ok($userProfession);
        } catch (Throwable $e) {
            return $this->fail('Failed to fetch userProfession', $e->getMessage(), 500);
        }
    }

    public function store(Request $request) {
        try {
            $instance = new UserProfession();
            $data = $this->filterData($request, $instance);

            return DB::transaction(function() use ($data) {
                $created = UserProfession::create($data);
                return $this->ok($created, 'UserProfession created', 201);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to create userProfession', $e->getMessage(), 422);
        }
    }

    public function update(Request $request, UserProfession $userProfession) {
        try {
            $data = $this->filterData($request, $userProfession);

            return DB::transaction(function() use ($data, $userProfession) {
                $userProfession->update($data);
                return $this->ok($userProfession->fresh(), 'UserProfession updated');
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to update userProfession', $e->getMessage(), 422);
        }
    }

    public function destroy(UserProfession $userProfession) {
        try {
            return DB::transaction(function() use ($userProfession) {
                $userProfession->delete();
                return $this->ok(null, 'UserProfession deleted', 200);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to delete userProfession', $e->getMessage(), 500);
        }
    }
}
