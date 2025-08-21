<?php

namespace App\Http\Controllers\Api;

use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class PackageController extends BaseApiController {

    public function index(Request $request) {
        try {
            $perPage = (int) $request->get('per_page', 20);
            $perPage = $perPage > 0 ? min($perPage, 100) : 20;

            $query = Package::query();

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
            return $this->fail('Failed to fetch package list', $e->getMessage(), 500);
        }
    }

    public function show(Package $package) {
        try {
            return $this->ok($package);
        } catch (Throwable $e) {
            return $this->fail('Failed to fetch package', $e->getMessage(), 500);
        }
    }

    public function store(Request $request) {
        try {
            $instance = new Package();
            $data = $this->filterData($request, $instance);

            return DB::transaction(function() use ($data) {
                $created = Package::create($data);
                return $this->ok($created, 'Package created', 201);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to create package', $e->getMessage(), 422);
        }
    }

    public function update(Request $request, Package $package) {
        try {
            $data = $this->filterData($request, $package);

            return DB::transaction(function() use ($data, $package) {
                $package->update($data);
                return $this->ok($package->fresh(), 'Package updated');
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to update package', $e->getMessage(), 422);
        }
    }

    public function destroy(Package $package) {
        try {
            return DB::transaction(function() use ($package) {
                $package->delete();
                return $this->ok(null, 'Package deleted', 200);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to delete package', $e->getMessage(), 500);
        }
    }
}
