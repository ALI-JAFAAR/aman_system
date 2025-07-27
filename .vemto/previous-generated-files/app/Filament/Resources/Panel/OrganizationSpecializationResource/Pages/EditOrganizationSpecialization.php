<?php

namespace App\Filament\Resources\Panel\OrganizationSpecializationResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\Panel\OrganizationSpecializationResource;

class EditOrganizationSpecialization extends EditRecord
{
    protected static string $resource = OrganizationSpecializationResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
