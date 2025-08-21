<?php

namespace App\Http\Controllers\Api;

use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class VehicleController extends BaseApiController {

    public function index(Request $request) {
        try {
            $perPage = (int) $request->get('per_page', 20);
            $perPage = $perPage > 0 ? min($perPage, 100) : 20;

            $query = Vehicle::query();

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
            return $this->fail('Failed to fetch vehicle list', $e->getMessage(), 500);
        }
    }

    public function show(Vehicle $vehicle) {
        try {
            return $this->ok($vehicle);
        } catch (Throwable $e) {
            return $this->fail('Failed to fetch vehicle', $e->getMessage(), 500);
        }
    }

    public function store(Request $request) {
        try {
            $instance = new Vehicle();
            $data = $this->filterData($request, $instance);

            return DB::transaction(function() use ($data) {
                $created = Vehicle::create($data);
                return $this->ok($created, 'Vehicle created', 201);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to create vehicle', $e->getMessage(), 422);
        }
    }

    public function update(Request $request, Vehicle $vehicle) {
        try {
            $data = $this->filterData($request, $vehicle);

            return DB::transaction(function() use ($data, $vehicle) {
                $vehicle->update($data);
                return $this->ok($vehicle->fresh(), 'Vehicle updated');
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to update vehicle', $e->getMessage(), 422);
        }
    }

    public function destroy(Vehicle $vehicle) {
        try {
            return DB::transaction(function() use ($vehicle) {
                $vehicle->delete();
                return $this->ok(null, 'Vehicle deleted', 200);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to delete vehicle', $e->getMessage(), 500);
        }
    }
}
