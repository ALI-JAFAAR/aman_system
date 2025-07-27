<?php

namespace App\Filament\Resources\Panel\UserOfferingResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Panel\UserOfferingResource;

class ListUserOfferings extends ListRecords
{
    protected static string $resource = UserOfferingResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
