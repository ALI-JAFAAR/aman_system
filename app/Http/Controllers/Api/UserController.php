<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class UserController extends BaseApiController {

    public function index(Request $request) {
        try {
            $perPage = (int) $request->get('per_page', 20);
            $perPage = $perPage > 0 ? min($perPage, 100) : 20;

            $query = User::query();

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
            return $this->fail('Failed to fetch user list', $e->getMessage(), 500);
        }
    }

    public function show(User $user) {
        try {
            return $this->ok($user);
        } catch (Throwable $e) {
            return $this->fail('Failed to fetch user', $e->getMessage(), 500);
        }
    }

    public function store(Request $request) {
        try {
            $instance = new User();
            $data = $this->filterData($request, $instance);

            return DB::transaction(function() use ($data) {
                $created = User::create($data);
                return $this->ok($created, 'User created', 201);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to create user', $e->getMessage(), 422);
        }
    }

    public function update(Request $request, User $user) {
        try {
            $data = $this->filterData($request, $user);

            return DB::transaction(function() use ($data, $user) {
                $user->update($data);
                return $this->ok($user->fresh(), 'User updated');
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to update user', $e->getMessage(), 422);
        }
    }

    public function destroy(User $user) {
        try {
            return DB::transaction(function() use ($user) {
                $user->delete();
                return $this->ok(null, 'User deleted', 200);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to delete user', $e->getMessage(), 500);
        }
    }
}
