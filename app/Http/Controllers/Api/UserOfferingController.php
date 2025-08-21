<?php

namespace App\Http\Controllers\Api;

use App\Models\UserOffering;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class UserOfferingController extends BaseApiController {

    public function index(Request $request) {
        try {
            $perPage = (int) $request->get('per_page', 20);
            $perPage = $perPage > 0 ? min($perPage, 100) : 20;

            $query = UserOffering::query();

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
            return $this->fail('Failed to fetch userOffering list', $e->getMessage(), 500);
        }
    }

    public function show(UserOffering $userOffering) {
        try {
            return $this->ok($userOffering);
        } catch (Throwable $e) {
            return $this->fail('Failed to fetch userOffering', $e->getMessage(), 500);
        }
    }

    public function store(Request $request) {
        try {
            $instance = new UserOffering();
            $data = $this->filterData($request, $instance);

            return DB::transaction(function() use ($data) {
                $created = UserOffering::create($data);
                return $this->ok($created, 'UserOffering created', 201);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to create userOffering', $e->getMessage(), 422);
        }
    }

    public function update(Request $request, UserOffering $userOffering) {
        try {
            $data = $this->filterData($request, $userOffering);

            return DB::transaction(function() use ($data, $userOffering) {
                $userOffering->update($data);
                return $this->ok($userOffering->fresh(), 'UserOffering updated');
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to update userOffering', $e->getMessage(), 422);
        }
    }

    public function destroy(UserOffering $userOffering) {
        try {
            return DB::transaction(function() use ($userOffering) {
                $userOffering->delete();
                return $this->ok(null, 'UserOffering deleted', 200);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to delete userOffering', $e->getMessage(), 500);
        }
    }
}
