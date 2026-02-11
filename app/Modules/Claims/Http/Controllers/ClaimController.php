<?php

namespace App\Modules\Claims\Http\Controllers;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Claim;
use App\Modules\Claims\Http\Requests\StoreClaimRequest;
use App\Modules\Claims\Http\Requests\UpdateClaimRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class ClaimController extends BaseApiController
{
    public function index(Request $request)
    {
        try {
            $perPage = (int) $request->integer('per_page', 20);
            $perPage = $perPage > 0 ? min($perPage, 100) : 20;

            $query = Claim::query()
                ->with([
                    'userOffering.user',
                    'userOffering.partnerOffering.organization',
                    'userOffering.partnerOffering.package',
                    'claimResponses',
                ])
                ->latest('id');

            if ($type = $request->get('type')) {
                $query->where('type', (string) $type);
            }
            if ($status = $request->get('status')) {
                $query->where('status', (string) $status);
            }
            if ($userId = $request->get('user_id')) {
                $query->whereHas('userOffering', fn ($q) => $q->where('user_id', (int) $userId));
            }

            return $this->ok($query->paginate($perPage));
        } catch (Throwable $e) {
            return $this->fail('Failed to fetch claims', $e->getMessage(), 500);
        }
    }

    public function show(Claim $claim)
    {
        $claim->loadMissing([
            'userOffering.user',
            'userOffering.partnerOffering.organization',
            'userOffering.partnerOffering.package',
            'claimResponses',
        ]);

        return $this->ok($claim);
    }

    public function store(StoreClaimRequest $request)
    {
        try {
            return DB::transaction(function () use ($request) {
                $data = $request->validated();
                $data['status'] = $data['status'] ?? 'pending';
                $data['submitted_at'] = $data['submitted_at'] ?? now();
                $data['resolution_amount'] = $data['resolution_amount'] ?? 0;
                $data['resolution_note'] = $data['resolution_note'] ?? null;

                $claim = Claim::create($data);

                return $this->ok($claim->fresh(), 'Claim created', 201);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to create claim', $e->getMessage(), 422);
        }
    }

    public function update(UpdateClaimRequest $request, Claim $claim)
    {
        try {
            return DB::transaction(function () use ($request, $claim) {
                $claim->update($request->validated());
                return $this->ok($claim->fresh(), 'Claim updated');
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to update claim', $e->getMessage(), 422);
        }
    }

    public function destroy(Claim $claim)
    {
        try {
            return DB::transaction(function () use ($claim) {
                $claim->delete();
                return $this->ok(null, 'Claim deleted');
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to delete claim', $e->getMessage(), 500);
        }
    }

    public function approve(Request $request, Claim $claim)
    {
        $data = $request->validate([
            'resolution_amount' => ['required', 'numeric', 'min:0'],
            'resolution_note' => ['nullable', 'string'],
        ]);

        $claim->update([
            'status' => 'approved',
            'resolution_amount' => (float) $data['resolution_amount'],
            'resolution_note' => $data['resolution_note'] ?? null,
        ]);

        return $this->ok($claim->fresh(), 'Claim approved');
    }

    public function reject(Request $request, Claim $claim)
    {
        $data = $request->validate([
            'resolution_note' => ['required', 'string'],
        ]);

        $claim->update([
            'status' => 'rejected',
            'resolution_amount' => 0,
            'resolution_note' => $data['resolution_note'] ?? null,
        ]);

        return $this->ok($claim->fresh(), 'Claim rejected');
    }

    public function close(Claim $claim)
    {
        $claim->update(['status' => 'closed']);
        return $this->ok($claim->fresh(), 'Claim closed');
    }
}

