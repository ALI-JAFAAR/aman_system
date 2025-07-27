<?php

namespace App\Filament\Resources\Panel\AdministrativeRecordResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Panel\AdministrativeRecordResource;

class ListAdministrativeRecords extends ListRecords
{
    protected static string $resource = AdministrativeRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
