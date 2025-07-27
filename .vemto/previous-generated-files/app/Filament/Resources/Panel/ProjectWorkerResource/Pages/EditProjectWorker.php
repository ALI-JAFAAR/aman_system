<?php

namespace App\Filament\Resources\Panel\ProjectWorkerResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\Panel\ProjectWorkerResource;

class EditProjectWorker extends EditRecord
{
    protected static string $resource = ProjectWorkerResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
