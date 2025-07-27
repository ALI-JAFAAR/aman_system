<?php

namespace App\Filament\Resources\Panel\UserProfessionResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\Panel\UserProfessionResource;

class EditUserProfession extends EditRecord
{
    protected static string $resource = UserProfessionResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
