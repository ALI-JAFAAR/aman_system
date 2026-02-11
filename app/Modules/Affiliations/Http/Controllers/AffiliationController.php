<?php

namespace App\Modules\Affiliations\Http\Controllers;

use App\Http\Controllers\Api\BaseApiController;
use App\Models\User;
use App\Models\UserAffiliation;
use App\Modules\Affiliations\Http\Requests\StoreAffiliationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class AffiliationController extends BaseApiController
{
    public function store(StoreAffiliationRequest $request)
    {
        try {
            return DB::transaction(function () use ($request) {
                $aff = UserAffiliation::create($request->validated());

                return $this->ok($aff->fresh(['organization', 'user']), 'Affiliation created', 201);
            });
        } catch (Throwable $e) {
            return $this->fail('Failed to create affiliation', $e->getMessage(), 422);
        }
    }

    public function listForUser(Request $request, User $user)
    {
        try {
            $perPage = (int) $request->integer('per_page', 50);
            $perPage = $perPage > 0 ? min($perPage, 100) : 50;

            $query = $user->userAffiliations()
                ->with('organization')
                ->latest('id');

            return $this->ok($query->paginate($perPage));
        } catch (Throwable $e) {
            return $this->fail('Failed to fetch affiliations', $e->getMessage(), 500);
        }
    }
}

