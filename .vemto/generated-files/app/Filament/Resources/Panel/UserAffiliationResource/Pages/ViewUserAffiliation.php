<?php

namespace App\Filament\Resources\Panel\UserAffiliationResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\Panel\UserAffiliationResource;

class ViewUserAffiliation extends ViewRecord
{
    protected static string $resource = UserAffiliationResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\EditAction::make()];
    }
}
