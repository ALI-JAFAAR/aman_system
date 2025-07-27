<?php

namespace App\Filament\Resources\Panel\AdministrativeRecordResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\Panel\AdministrativeRecordResource;

class EditAdministrativeRecord extends EditRecord
{
    protected static string $resource = AdministrativeRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
