<?php

namespace App\Http\Controllers\Api;

use App\Models\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class UserServiceController extends BaseApiController {

    public function index(Request $request) {
        try {
            $perPage = (int) $request->get('per_page', 20);
            $perPage = $perPage > 0 ? min($perPage, 100) : 20;

            $query = UserService::query();

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
            return $this->fail('Failed to fetch userService list', $e->getMessage(), 500);
        }
    }

    public function show(UserService $userService) {
        try {
            return $this->ok($userService);
        } catch (Throwable $e) {
            return $this->fail('Failed to fetch userService', $e->getMessage(), 500);
        }
    }

    public function store(Request $request) {
        try {
            $instance = new UserService();
            $data = $this->filterData($request, $instance);

            return DB::transaction(function() use ($data) {
                $created = UserService::create($data);
                return $this->ok($created, 'UserService created', 201);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to create userService', $e->getMessage(), 422);
        }
    }

    public function update(Request $request, UserService $userService) {
        try {
            $data = $this->filterData($request, $userService);

            return DB::transaction(function() use ($data, $userService) {
                $userService->update($data);
                return $this->ok($userService->fresh(), 'UserService updated');
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to update userService', $e->getMessage(), 422);
        }
    }

    public function destroy(UserService $userService) {
        try {
            return DB::transaction(function() use ($userService) {
                $userService->delete();
                return $this->ok(null, 'UserService deleted', 200);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to delete userService', $e->getMessage(), 500);
        }
    }
}
