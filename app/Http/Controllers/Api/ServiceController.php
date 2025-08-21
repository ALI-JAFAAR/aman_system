<?php

namespace App\Http\Controllers\Api;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class ServiceController extends BaseApiController {

    public function index(Request $request) {
        try {
            $perPage = (int) $request->get('per_page', 20);
            $perPage = $perPage > 0 ? min($perPage, 100) : 20;

            $query = Service::query();

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
            return $this->fail('Failed to fetch service list', $e->getMessage(), 500);
        }
    }

    public function show(Service $service) {
        try {
            return $this->ok($service);
        } catch (Throwable $e) {
            return $this->fail('Failed to fetch service', $e->getMessage(), 500);
        }
    }

    public function store(Request $request) {
        try {
            $instance = new Service();
            $data = $this->filterData($request, $instance);

            return DB::transaction(function() use ($data) {
                $created = Service::create($data);
                return $this->ok($created, 'Service created', 201);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to create service', $e->getMessage(), 422);
        }
    }

    public function update(Request $request, Service $service) {
        try {
            $data = $this->filterData($request, $service);

            return DB::transaction(function() use ($data, $service) {
                $service->update($data);
                return $this->ok($service->fresh(), 'Service updated');
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to update service', $e->getMessage(), 422);
        }
    }

    public function destroy(Service $service) {
        try {
            return DB::transaction(function() use ($service) {
                $service->delete();
                return $this->ok(null, 'Service deleted', 200);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to delete service', $e->getMessage(), 500);
        }
    }
}
