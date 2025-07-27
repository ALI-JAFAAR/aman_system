<?php

namespace App\Filament\Resources\Panel\SpecializationResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Panel\SpecializationResource;

class ListSpecializations extends ListRecords
{
    protected static string $resource = SpecializationResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
