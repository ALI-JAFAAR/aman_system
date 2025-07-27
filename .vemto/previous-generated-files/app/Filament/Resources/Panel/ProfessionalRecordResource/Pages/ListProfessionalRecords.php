<?php

namespace App\Filament\Resources\Panel\ProfessionalRecordResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Panel\ProfessionalRecordResource;

class ListProfessionalRecords extends ListRecords
{
    protected static string $resource = ProfessionalRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
