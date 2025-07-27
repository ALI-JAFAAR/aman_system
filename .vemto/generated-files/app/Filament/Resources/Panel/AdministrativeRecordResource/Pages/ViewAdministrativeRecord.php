<?php

namespace App\Filament\Resources\Panel\AdministrativeRecordResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\Panel\AdministrativeRecordResource;

class ViewAdministrativeRecord extends ViewRecord
{
    protected static string $resource = AdministrativeRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\EditAction::make()];
    }
}
