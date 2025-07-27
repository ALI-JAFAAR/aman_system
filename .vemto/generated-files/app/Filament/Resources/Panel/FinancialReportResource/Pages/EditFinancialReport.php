<?php

namespace App\Filament\Resources\Panel\FinancialReportResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\Panel\FinancialReportResource;

class EditFinancialReport extends EditRecord
{
    protected static string $resource = FinancialReportResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
