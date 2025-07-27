<?php

namespace App\Filament\Resources\Panel\OfferingDistributionResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Panel\OfferingDistributionResource;

class ListOfferingDistributions extends ListRecords
{
    protected static string $resource = OfferingDistributionResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
