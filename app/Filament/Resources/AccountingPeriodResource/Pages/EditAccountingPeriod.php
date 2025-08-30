<?php

namespace App\Filament\Resources\AccountingPeriodResource\Pages;

use App\Filament\Resources\AccountingPeriodResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAccountingPeriod extends EditRecord
{
    protected static string $resource = AccountingPeriodResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
