<?php

namespace App\Filament\Pages;

use App\Models\LedgerEntry;
use App\Services\AffiliationPostingService as COA;
use Filament\Pages\Page;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;

class CashboxOverview extends Page{
    protected static ?string $navigationGroup = 'الفوترة والمالية';
    protected static ?string $navigationLabel = 'الخزينة / الصندوق';
    protected static ?int    $navigationSort  = 40;
    protected static ?string $navigationIcon  = 'heroicon-o-banknotes';


    protected static string $view = 'filament.pages.cashbox-overview';

    public static function getNavigationBadge(): ?string
    {
        return null;
    }
}
