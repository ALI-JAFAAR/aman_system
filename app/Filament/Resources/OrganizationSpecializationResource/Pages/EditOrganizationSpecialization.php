<?php

namespace App\Filament\Resources\OrganizationSpecializationResource\Pages;

use App\Filament\Resources\OrganizationSpecializationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOrganizationSpecialization extends EditRecord
{
    protected static string $resource = OrganizationSpecializationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
