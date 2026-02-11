<?php

namespace App\Modules\Users\Http\Controllers;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\User;
use App\Modules\Users\Http\Requests\StoreUserRequest;
use App\Modules\Users\Http\Requests\UpdateUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class UserController extends BaseApiController
{
    public function index(Request $request)
    {
        try {
            $perPage = (int) $request->integer('per_page', 20);
            $perPage = $perPage > 0 ? min($perPage, 100) : 20;

            $query = User::query()
                ->with([
                    'userProfiles',
                    'employees.organization',
                ])
                ->latest('id');

            if ($search = trim((string) $request->get('q', ''))) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('id', 'like', "%{$search}%");
                });
            }

            return $this->ok($query->paginate($perPage));
        } catch (Throwable $e) {
            return $this->fail('Failed to fetch users', $e->getMessage(), 500);
        }
    }

    public function show(User $user)
    {
        try {
            $user->loadMissing([
                'userProfiles',
                'employees.organization',
                'userAffiliations.organization',
            ]);

            return $this->ok($user);
        } catch (Throwable $e) {
            return $this->fail('Failed to fetch user', $e->getMessage(), 500);
        }
    }

    public function store(StoreUserRequest $request)
    {
        try {
            return DB::transaction(function () use ($request) {
                $user = User::create($request->validated());

                return $this->ok($user->fresh(), 'User created', 201);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to create user', $e->getMessage(), 422);
        }
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        try {
            return DB::transaction(function () use ($request, $user) {
                $user->update($request->validated());

                return $this->ok($user->fresh(), 'User updated');
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to update user', $e->getMessage(), 422);
        }
    }

    public function destroy(User $user)
    {
        try {
            return DB::transaction(function () use ($user) {
                $user->delete();

                return $this->ok(null, 'User deleted');
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to delete user', $e->getMessage(), 500);
        }
    }
}

