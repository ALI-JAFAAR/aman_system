<?php

namespace App\Filament\Resources\Panel\UserProfessionResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\Panel\UserProfessionResource;

class ViewUserProfession extends ViewRecord
{
    protected static string $resource = UserProfessionResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\EditAction::make()];
    }
}
