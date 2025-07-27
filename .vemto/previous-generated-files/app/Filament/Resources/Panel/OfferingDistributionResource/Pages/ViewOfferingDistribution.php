<?php

namespace App\Filament\Resources\Panel\OfferingDistributionResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\Panel\OfferingDistributionResource;

class ViewOfferingDistribution extends ViewRecord
{
    protected static string $resource = OfferingDistributionResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\EditAction::make()];
    }
}
