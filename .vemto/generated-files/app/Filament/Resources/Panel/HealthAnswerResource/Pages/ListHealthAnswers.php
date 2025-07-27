<?php

namespace App\Filament\Resources\Panel\HealthAnswerResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Panel\HealthAnswerResource;

class ListHealthAnswers extends ListRecords
{
    protected static string $resource = HealthAnswerResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
