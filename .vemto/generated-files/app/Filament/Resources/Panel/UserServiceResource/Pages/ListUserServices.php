<?php

namespace App\Filament\Resources\Panel\UserServiceResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Panel\UserServiceResource;

class ListUserServices extends ListRecords
{
    protected static string $resource = UserServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
