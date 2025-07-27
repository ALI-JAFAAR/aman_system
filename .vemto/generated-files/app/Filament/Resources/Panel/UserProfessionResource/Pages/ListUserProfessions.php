<?php

namespace App\Filament\Resources\Panel\UserProfessionResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Panel\UserProfessionResource;

class ListUserProfessions extends ListRecords
{
    protected static string $resource = UserProfessionResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
