<?php

namespace App\Filament\Resources\ClaimResource\Pages;

use App\Filament\Resources\ClaimResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateClaim extends CreateRecord{

    protected static string $resource = ClaimResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // member_id حقل واجهة فقط إن كنت تستخدمه لاختبار المنتسب
        unset($data['member_id']);

        $data['status']            = 'pending';
        $data['submitted_at']      = now();

        // 👈 الحقول الموجودة عندك والتي سببت الخطأ:
        $data['resolution_amount'] = 0;      // إلزامي في DB
        $data['resolution_note']   = null;   // إن كان Nullable في DB

        return $data;
    }

}
