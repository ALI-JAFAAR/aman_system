<?php

namespace App\Modules\Claims\Http\Controllers;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\Claim;
use App\Models\ClaimResponse;
use App\Modules\Claims\Http\Requests\StoreClaimResponseRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class ClaimResponseController extends BaseApiController
{
    public function index(Request $request, Claim $claim)
    {
        try {
            $perPage = (int) $request->integer('per_page', 50);
            $perPage = $perPage > 0 ? min($perPage, 100) : 50;

            $query = $claim->claimResponses()->latest('id');

            return $this->ok($query->paginate($perPage));
        } catch (Throwable $e) {
            return $this->fail('Failed to fetch claim responses', $e->getMessage(), 500);
        }
    }

    public function store(StoreClaimResponseRequest $request, Claim $claim)
    {
        try {
            return DB::transaction(function () use ($request, $claim) {
                $data = $request->validated();
                $data['claim_id'] = $claim->id;

                $resp = ClaimResponse::create($data);

                return $this->ok($resp->fresh(), 'Response created', 201);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to create claim response', $e->getMessage(), 422);
        }
    }
}

