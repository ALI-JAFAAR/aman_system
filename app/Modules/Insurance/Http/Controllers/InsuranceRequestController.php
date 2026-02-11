<?php

namespace App\Modules\Insurance\Http\Controllers;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Employee;
use App\Models\UserOffering;
use App\Enums\OrganizationType;
use App\Modules\Insurance\Http\Requests\UpdateInsuranceRequestRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class InsuranceRequestController extends BaseApiController
{
    public function index(Request $request)
    {
        try {
            $perPage = (int) $request->integer('per_page', 20);
            $perPage = $perPage > 0 ? min($perPage, 100) : 20;

            $q = UserOffering::query()
                ->with(['user', 'partnerOffering.organization', 'partnerOffering.package'])
                ->whereHas('partnerOffering', fn ($qq) => $qq->where('partner_must_fill_number', true))
                ->latest('id');

            // Optional filters
            if ($status = $request->get('status')) {
                $q->where('status', (string) $status);
            }
            if ($needsNumber = $request->get('needs_number')) {
                if ($needsNumber === '1' || $needsNumber === 'true') {
                    $q->whereNull('partner_filled_number');
                } elseif ($needsNumber === '0' || $needsNumber === 'false') {
                    $q->whereNotNull('partner_filled_number');
                }
            }

            // Scope to partner org ONLY if logged-in employee is from an insurance company
            if ($userId = auth()->id()) {
                $emp = Employee::with('organization')->where('user_id', $userId)->first();
                if ($emp && $emp->organization && $emp->organization->type === OrganizationType::INSURANCE_COMPANY) {
                    $q->whereHas('partnerOffering', fn ($qq) => $qq->where('organization_id', $emp->organization_id));
                }
            }

            return $this->ok($q->paginate($perPage));
        } catch (Throwable $e) {
            return $this->fail('Failed to fetch insurance requests', $e->getMessage(), 500);
        }
    }

    public function show(UserOffering $insuranceRequest)
    {
        $insuranceRequest->loadMissing(['user', 'partnerOffering.organization', 'partnerOffering.package']);
        return $this->ok($insuranceRequest);
    }

    public function update(UpdateInsuranceRequestRequest $request, UserOffering $insuranceRequest)
    {
        try {
            return DB::transaction(function () use ($request, $insuranceRequest) {
                $data = $request->validated();

                // If partner number filled, auto-activate (same behavior as Filament edit page)
                if (array_key_exists('partner_filled_number', $data) && ! empty($data['partner_filled_number'])) {
                    $data['activated_at'] = $data['activated_at'] ?? now();
                    $data['status'] = $data['status'] ?? 'active';
                }

                $insuranceRequest->update($data);

                return $this->ok($insuranceRequest->fresh(), 'Insurance request updated');
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to update insurance request', $e->getMessage(), 422);
        }
    }
}

