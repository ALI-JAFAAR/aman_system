<?php

namespace App\Http\Controllers\Api;

use App\Models\PartnerOffering;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class PartnerOfferingController extends BaseApiController {

    public function index(Request $request) {
        try {
            $perPage = (int) $request->get('per_page', 20);
            $perPage = $perPage > 0 ? min($perPage, 100) : 20;

            $query = PartnerOffering::query();

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
            return $this->fail('Failed to fetch partnerOffering list', $e->getMessage(), 500);
        }
    }

    public function show(PartnerOffering $partnerOffering) {
        try {
            return $this->ok($partnerOffering);
        } catch (Throwable $e) {
            return $this->fail('Failed to fetch partnerOffering', $e->getMessage(), 500);
        }
    }

    public function store(Request $request) {
        try {
            $instance = new PartnerOffering();
            $data = $this->filterData($request, $instance);

            return DB::transaction(function() use ($data) {
                $created = PartnerOffering::create($data);
                return $this->ok($created, 'PartnerOffering created', 201);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to create partnerOffering', $e->getMessage(), 422);
        }
    }

    public function update(Request $request, PartnerOffering $partnerOffering) {
        try {
            $data = $this->filterData($request, $partnerOffering);

            return DB::transaction(function() use ($data, $partnerOffering) {
                $partnerOffering->update($data);
                return $this->ok($partnerOffering->fresh(), 'PartnerOffering updated');
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to update partnerOffering', $e->getMessage(), 422);
        }
    }

    public function destroy(PartnerOffering $partnerOffering) {
        try {
            return DB::transaction(function() use ($partnerOffering) {
                $partnerOffering->delete();
                return $this->ok(null, 'PartnerOffering deleted', 200);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to delete partnerOffering', $e->getMessage(), 500);
        }
    }
}
