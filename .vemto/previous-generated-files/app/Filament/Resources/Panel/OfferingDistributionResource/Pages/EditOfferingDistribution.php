<?php

namespace App\Filament\Resources\Panel\OfferingDistributionResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\Panel\OfferingDistributionResource;

class EditOfferingDistribution extends EditRecord
{
    protected static string $resource = OfferingDistributionResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
