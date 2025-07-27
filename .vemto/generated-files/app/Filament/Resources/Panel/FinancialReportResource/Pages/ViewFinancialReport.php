<?php

namespace App\Filament\Resources\Panel\FinancialReportResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\Panel\FinancialReportResource;

class ViewFinancialReport extends ViewRecord
{
    protected static string $resource = FinancialReportResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\EditAction::make()];
    }
}
