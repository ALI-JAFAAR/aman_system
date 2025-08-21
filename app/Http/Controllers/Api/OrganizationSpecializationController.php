<?php

namespace App\Http\Controllers\Api;

use App\Models\OrganizationSpecialization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class OrganizationSpecializationController extends BaseApiController {

    public function index(Request $request) {
        try {
            $perPage = (int) $request->get('per_page', 20);
            $perPage = $perPage > 0 ? min($perPage, 100) : 20;

            $query = OrganizationSpecialization::query();

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
            return $this->fail('Failed to fetch organizationSpecialization list', $e->getMessage(), 500);
        }
    }

    public function show(OrganizationSpecialization $organizationSpecialization) {
        try {
            return $this->ok($organizationSpecialization);
        } catch (Throwable $e) {
            return $this->fail('Failed to fetch organizationSpecialization', $e->getMessage(), 500);
        }
    }

    public function store(Request $request) {
        try {
            $instance = new OrganizationSpecialization();
            $data = $this->filterData($request, $instance);

            return DB::transaction(function() use ($data) {
                $created = OrganizationSpecialization::create($data);
                return $this->ok($created, 'OrganizationSpecialization created', 201);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to create organizationSpecialization', $e->getMessage(), 422);
        }
    }

    public function update(Request $request, OrganizationSpecialization $organizationSpecialization) {
        try {
            $data = $this->filterData($request, $organizationSpecialization);

            return DB::transaction(function() use ($data, $organizationSpecialization) {
                $organizationSpecialization->update($data);
                return $this->ok($organizationSpecialization->fresh(), 'OrganizationSpecialization updated');
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to update organizationSpecialization', $e->getMessage(), 422);
        }
    }

    public function destroy(OrganizationSpecialization $organizationSpecialization) {
        try {
            return DB::transaction(function() use ($organizationSpecialization) {
                $organizationSpecialization->delete();
                return $this->ok(null, 'OrganizationSpecialization deleted', 200);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to delete organizationSpecialization', $e->getMessage(), 500);
        }
    }
}
