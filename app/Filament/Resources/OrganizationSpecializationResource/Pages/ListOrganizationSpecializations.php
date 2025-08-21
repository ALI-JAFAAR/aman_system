<?php

namespace App\Filament\Resources\OrganizationSpecializationResource\Pages;

use App\Filament\Resources\OrganizationSpecializationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOrganizationSpecializations extends ListRecords
{
    protected static string $resource = OrganizationSpecializationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
