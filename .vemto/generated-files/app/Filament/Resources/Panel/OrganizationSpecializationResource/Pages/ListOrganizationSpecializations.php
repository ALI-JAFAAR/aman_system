<?php

namespace App\Filament\Resources\Panel\OrganizationSpecializationResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Panel\OrganizationSpecializationResource;

class ListOrganizationSpecializations extends ListRecords
{
    protected static string $resource = OrganizationSpecializationResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
