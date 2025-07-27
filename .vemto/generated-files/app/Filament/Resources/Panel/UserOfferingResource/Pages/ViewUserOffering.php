<?php

namespace App\Filament\Resources\Panel\UserOfferingResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\Panel\UserOfferingResource;

class ViewUserOffering extends ViewRecord
{
    protected static string $resource = UserOfferingResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\EditAction::make()];
    }
}
