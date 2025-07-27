<?php

namespace App\Filament\Resources\Panel\UserAffiliationResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\Panel\UserAffiliationResource;

class EditUserAffiliation extends EditRecord
{
    protected static string $resource = UserAffiliationResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
