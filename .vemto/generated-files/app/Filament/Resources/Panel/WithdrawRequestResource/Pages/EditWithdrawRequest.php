<?php

namespace App\Filament\Resources\Panel\WithdrawRequestResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\Panel\WithdrawRequestResource;

class EditWithdrawRequest extends EditRecord
{
    protected static string $resource = WithdrawRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
