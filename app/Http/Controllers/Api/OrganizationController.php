<?php

namespace App\Http\Controllers\Api;

use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class OrganizationController extends BaseApiController {

    public function index(Request $request) {
        try {
            $perPage = (int) $request->get('per_page', 20);
            $perPage = $perPage > 0 ? min($perPage, 100) : 20;

            $query = Organization::query();

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
            return $this->fail('Failed to fetch organization list', $e->getMessage(), 500);
        }
    }

    public function show(Organization $organization) {
        try {
            return $this->ok($organization);
        } catch (Throwable $e) {
            return $this->fail('Failed to fetch organization', $e->getMessage(), 500);
        }
    }

    public function store(Request $request) {
        try {
            $instance = new Organization();
            $data = $this->filterData($request, $instance);

            return DB::transaction(function() use ($data) {
                $created = Organization::create($data);
                return $this->ok($created, 'Organization created', 201);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to create organization', $e->getMessage(), 422);
        }
    }

    public function update(Request $request, Organization $organization) {
        try {
            $data = $this->filterData($request, $organization);

            return DB::transaction(function() use ($data, $organization) {
                $organization->update($data);
                return $this->ok($organization->fresh(), 'Organization updated');
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to update organization', $e->getMessage(), 422);
        }
    }

    public function destroy(Organization $organization) {
        try {
            return DB::transaction(function() use ($organization) {
                $organization->delete();
                return $this->ok(null, 'Organization deleted', 200);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to delete organization', $e->getMessage(), 500);
        }
    }
}
