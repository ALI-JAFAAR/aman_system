<?php

namespace App\Modules\Insurance\Http\Controllers;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\PartnerOffering;
use App\Modules\Insurance\Http\Requests\StorePartnerOfferingRequest;
use App\Modules\Insurance\Http\Requests\UpdatePartnerOfferingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class PartnerOfferingController extends BaseApiController
{
    public function index(Request $request)
    {
        try {
            $perPage = (int) $request->integer('per_page', 20);
            $perPage = $perPage > 0 ? min($perPage, 100) : 20;

            $query = PartnerOffering::query()
                ->with(['organization', 'package', 'distribution'])
                ->latest('id');

            if ($orgId = $request->get('organization_id')) {
                $query->where('organization_id', (int) $orgId);
            }
            if ($packageId = $request->get('package_id')) {
                $query->where('package_id', (int) $packageId);
            }

            return $this->ok($query->paginate($perPage));
        } catch (Throwable $e) {
            return $this->fail('Failed to fetch partner offerings', $e->getMessage(), 500);
        }
    }

    public function show(PartnerOffering $partnerOffering)
    {
        $partnerOffering->loadMissing(['organization', 'package', 'distribution']);
        return $this->ok($partnerOffering);
    }

    public function store(StorePartnerOfferingRequest $request)
    {
        try {
            return DB::transaction(function () use ($request) {
                $po = PartnerOffering::create($request->validated());
                return $this->ok($po->fresh(['organization', 'package']), 'Partner offering created', 201);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to create partner offering', $e->getMessage(), 422);
        }
    }

    public function update(UpdatePartnerOfferingRequest $request, PartnerOffering $partnerOffering)
    {
        try {
            return DB::transaction(function () use ($request, $partnerOffering) {
                $partnerOffering->update($request->validated());
                return $this->ok($partnerOffering->fresh(['organization', 'package']), 'Partner offering updated');
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to update partner offering', $e->getMessage(), 422);
        }
    }

    public function destroy(PartnerOffering $partnerOffering)
    {
        try {
            return DB::transaction(function () use ($partnerOffering) {
                $partnerOffering->delete();
                return $this->ok(null, 'Partner offering deleted');
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to delete partner offering', $e->getMessage(), 500);
        }
    }
}

