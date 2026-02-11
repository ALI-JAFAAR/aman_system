<?php

namespace App\Modules\Organizations\Http\Controllers;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Organization;
use App\Modules\Organizations\Http\Requests\StoreOrganizationRequest;
use App\Modules\Organizations\Http\Requests\UpdateOrganizationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class OrganizationController extends BaseApiController
{
    public function index(Request $request)
    {
        try {
            $perPage = (int) $request->integer('per_page', 20);
            $perPage = $perPage > 0 ? min($perPage, 100) : 20;

            $query = Organization::query()
                ->latest('id');

            if ($search = trim((string) $request->get('q', ''))) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('id', 'like', "%{$search}%");
                });
            }

            if ($type = $request->get('type')) {
                $query->where('type', $type);
            }

            return $this->ok($query->paginate($perPage));
        } catch (Throwable $e) {
            return $this->fail('Failed to fetch organizations', $e->getMessage(), 500);
        }
    }

    public function show(Organization $organization)
    {
        try {
            $organization->loadMissing([
                'organization', // parent
                'organizations', // children
                'employees.user',
            ]);

            return $this->ok($organization);
        } catch (Throwable $e) {
            return $this->fail('Failed to fetch organization', $e->getMessage(), 500);
        }
    }

    public function store(StoreOrganizationRequest $request)
    {
        try {
            return DB::transaction(function () use ($request) {
                $org = Organization::create($request->validated());

                return $this->ok($org->fresh(), 'Organization created', 201);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to create organization', $e->getMessage(), 422);
        }
    }

    public function update(UpdateOrganizationRequest $request, Organization $organization)
    {
        try {
            return DB::transaction(function () use ($request, $organization) {
                $organization->update($request->validated());

                return $this->ok($organization->fresh(), 'Organization updated');
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to update organization', $e->getMessage(), 422);
        }
    }

    public function destroy(Organization $organization)
    {
        try {
            return DB::transaction(function () use ($organization) {
                $organization->delete();

                return $this->ok(null, 'Organization deleted');
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to delete organization', $e->getMessage(), 500);
        }
    }
}

