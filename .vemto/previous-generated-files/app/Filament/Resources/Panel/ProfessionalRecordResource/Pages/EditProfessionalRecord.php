<?php

namespace App\Filament\Resources\Panel\ProfessionalRecordResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\Panel\ProfessionalRecordResource;

class EditProfessionalRecord extends EditRecord
{
    protected static string $resource = ProfessionalRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
