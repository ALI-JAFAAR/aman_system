<?php

namespace App\Filament\Resources\Panel\ProjectWorkerResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\Panel\ProjectWorkerResource;

class ViewProjectWorker extends ViewRecord
{
    protected static string $resource = ProjectWorkerResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\EditAction::make()];
    }
}
