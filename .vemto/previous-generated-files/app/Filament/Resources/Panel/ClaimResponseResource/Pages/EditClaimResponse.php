<?php

namespace App\Filament\Resources\Panel\ClaimResponseResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\Panel\ClaimResponseResource;

class EditClaimResponse extends EditRecord
{
    protected static string $resource = ClaimResponseResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
