<?php

namespace App\Filament\Resources\Panel\ClaimResponseResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\Panel\ClaimResponseResource;

class ViewClaimResponse extends ViewRecord
{
    protected static string $resource = ClaimResponseResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\EditAction::make()];
    }
}
