<?php

namespace App\Filament\Resources\Panel\PartnerOfferingResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\Panel\PartnerOfferingResource;

class EditPartnerOffering extends EditRecord
{
    protected static string $resource = PartnerOfferingResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
