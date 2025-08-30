<?php

namespace App\Filament\Resources\PartnerAccountResource\Pages;

use App\Filament\Resources\PartnerAccountResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPartnerAccount extends EditRecord
{
    protected static string $resource = PartnerAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
