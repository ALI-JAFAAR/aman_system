<?php

namespace App\Support;

trait ShowsOfferings{
    protected function offeringsStateForUserId(int $userId): array
    {
        return \App\Models\UserOffering::query()
            ->with([
                'partnerOffering:id,organization_id,package_id,price',
                'partnerOffering.organization:id,name',
                'partnerOffering.package:id,name',
            ])
            ->latest('id')
            ->get()
            ->map(function ($uo) {
                return [
                    'company'   => $uo->partnerOffering?->organization?->name,
                    'package'   => $uo->partnerOffering?->package?->name,
                    'price'     => (float)($uo->partnerOffering?->price ?? 0),
                    'status'    => strtolower((string)$uo->status), // applied|active|pending|rejected
                    'plat_no'   => $uo->platform_generated_number,
                    'partner_no'=> $uo->partner_filled_number,
                    'applied'   => optional($uo->applied_at)->format('Y-m-d'),
                    'activated' => optional($uo->activated_at)->format('Y-m-d'), // your table uses activated_at
                ];
            })
            ->all();
    }
}
