<?php

namespace App\Filament\Resources\Panel\OrganizationSpecializationResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\Panel\OrganizationSpecializationResource;

class ViewOrganizationSpecialization extends ViewRecord
{
    protected static string $resource = OrganizationSpecializationResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\EditAction::make()];
    }
}
