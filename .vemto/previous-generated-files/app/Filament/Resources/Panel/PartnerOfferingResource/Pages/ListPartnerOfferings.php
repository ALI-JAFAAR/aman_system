<?php

namespace App\Filament\Resources\Panel\PartnerOfferingResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Panel\PartnerOfferingResource;

class ListPartnerOfferings extends ListRecords
{
    protected static string $resource = PartnerOfferingResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
