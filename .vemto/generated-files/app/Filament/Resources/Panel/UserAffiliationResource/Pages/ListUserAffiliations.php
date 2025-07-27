<?php

namespace App\Filament\Resources\Panel\UserAffiliationResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Panel\UserAffiliationResource;

class ListUserAffiliations extends ListRecords
{
    protected static string $resource = UserAffiliationResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
