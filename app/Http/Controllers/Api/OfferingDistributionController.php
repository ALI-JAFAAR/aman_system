<?php

namespace App\Http\Controllers\Api;

use App\Models\OfferingDistribution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class OfferingDistributionController extends BaseApiController {

    public function index(Request $request) {
        try {
            $perPage = (int) $request->get('per_page', 20);
            $perPage = $perPage > 0 ? min($perPage, 100) : 20;

            $query = OfferingDistribution::query();

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
            return $this->fail('Failed to fetch offeringDistribution list', $e->getMessage(), 500);
        }
    }

    public function show(OfferingDistribution $offeringDistribution) {
        try {
            return $this->ok($offeringDistribution);
        } catch (Throwable $e) {
            return $this->fail('Failed to fetch offeringDistribution', $e->getMessage(), 500);
        }
    }

    public function store(Request $request) {
        try {
            $instance = new OfferingDistribution();
            $data = $this->filterData($request, $instance);

            return DB::transaction(function() use ($data) {
                $created = OfferingDistribution::create($data);
                return $this->ok($created, 'OfferingDistribution created', 201);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to create offeringDistribution', $e->getMessage(), 422);
        }
    }

    public function update(Request $request, OfferingDistribution $offeringDistribution) {
        try {
            $data = $this->filterData($request, $offeringDistribution);

            return DB::transaction(function() use ($data, $offeringDistribution) {
                $offeringDistribution->update($data);
                return $this->ok($offeringDistribution->fresh(), 'OfferingDistribution updated');
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to update offeringDistribution', $e->getMessage(), 422);
        }
    }

    public function destroy(OfferingDistribution $offeringDistribution) {
        try {
            return DB::transaction(function() use ($offeringDistribution) {
                $offeringDistribution->delete();
                return $this->ok(null, 'OfferingDistribution deleted', 200);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to delete offeringDistribution', $e->getMessage(), 500);
        }
    }
}
