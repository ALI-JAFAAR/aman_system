<?php

namespace App\Filament\Resources\Panel\ProfessionResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\Panel\ProfessionResource;

class EditProfession extends EditRecord
{
    protected static string $resource = ProfessionResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
