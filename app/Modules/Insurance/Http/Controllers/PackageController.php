<?php

namespace App\Modules\Insurance\Http\Controllers;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Package;
use App\Modules\Insurance\Http\Requests\StorePackageRequest;
use App\Modules\Insurance\Http\Requests\UpdatePackageRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class PackageController extends BaseApiController
{
    public function index(Request $request)
    {
        try {
            $perPage = (int) $request->integer('per_page', 20);
            $perPage = $perPage > 0 ? min($perPage, 100) : 20;

            $query = Package::query()->latest('id');

            if ($search = trim((string) $request->get('q', ''))) {
                $query->where('name', 'like', "%{$search}%");
            }

            return $this->ok($query->paginate($perPage));
        } catch (Throwable $e) {
            return $this->fail('Failed to fetch packages', $e->getMessage(), 500);
        }
    }

    public function show(Package $package)
    {
        return $this->ok($package);
    }

    public function store(StorePackageRequest $request)
    {
        try {
            return DB::transaction(function () use ($request) {
                $pkg = Package::create($request->validated());
                return $this->ok($pkg->fresh(), 'Package created', 201);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to create package', $e->getMessage(), 422);
        }
    }

    public function update(UpdatePackageRequest $request, Package $package)
    {
        try {
            return DB::transaction(function () use ($request, $package) {
                $package->update($request->validated());
                return $this->ok($package->fresh(), 'Package updated');
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to update package', $e->getMessage(), 422);
        }
    }

    public function destroy(Package $package)
    {
        try {
            return DB::transaction(function () use ($package) {
                $package->delete();
                return $this->ok(null, 'Package deleted');
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to delete package', $e->getMessage(), 500);
        }
    }
}
