<?php

namespace App\Filament\Resources\Panel\ProfessionalRecordResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\Panel\ProfessionalRecordResource;

class ViewProfessionalRecord extends ViewRecord
{
    protected static string $resource = ProfessionalRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\EditAction::make()];
    }
}
