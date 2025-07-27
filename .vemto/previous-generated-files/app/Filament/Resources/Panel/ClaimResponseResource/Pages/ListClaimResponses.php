<?php

namespace App\Filament\Resources\Panel\ClaimResponseResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\Panel\ClaimResponseResource;

class ListClaimResponses extends ListRecords
{
    protected static string $resource = ClaimResponseResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
