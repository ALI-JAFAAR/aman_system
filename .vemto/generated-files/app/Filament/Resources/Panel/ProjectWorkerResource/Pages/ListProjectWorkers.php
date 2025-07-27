<?php

namespace App\Filament\Resources\Panel\ProjectWorkerResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Panel\ProjectWorkerResource;

class ListProjectWorkers extends ListRecords
{
    protected static string $resource = ProjectWorkerResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
