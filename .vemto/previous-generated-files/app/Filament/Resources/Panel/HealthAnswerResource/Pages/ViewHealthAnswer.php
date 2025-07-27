<?php

namespace App\Filament\Resources\Panel\HealthAnswerResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\Panel\HealthAnswerResource;

class ViewHealthAnswer extends ViewRecord
{
    protected static string $resource = HealthAnswerResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\EditAction::make()];
    }
}
