<?php

namespace App\Filament\Resources\AffiliationResource\Pages;

use App\Filament\Resources\AffiliationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAffiliation extends EditRecord
{
    protected static string $resource = AffiliationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
