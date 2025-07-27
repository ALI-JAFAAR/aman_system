<?php

namespace App\Filament\Resources\Panel\ClaimResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Panel\ClaimResource;

class ListClaims extends ListRecords
{
    protected static string $resource = ClaimResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
