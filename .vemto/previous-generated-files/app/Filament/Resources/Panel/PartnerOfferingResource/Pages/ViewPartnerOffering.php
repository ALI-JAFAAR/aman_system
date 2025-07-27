<?php

namespace App\Filament\Resources\Panel\PartnerOfferingResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\Panel\PartnerOfferingResource;

class ViewPartnerOffering extends ViewRecord
{
    protected static string $resource = PartnerOfferingResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\EditAction::make()];
    }
}
