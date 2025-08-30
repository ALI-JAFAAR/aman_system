<?php
namespace App\Filament\Resources\ReconciliationResource\Pages;

use App\Filament\Resources\ReconciliationResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;

class ViewReconciliation extends ViewRecord{

    protected static string $resource = ReconciliationResource::class;

    public function infolist(Infolist $infolist): Infolist{
        return $infolist->schema([
            Section::make('الملخص')->schema([
                TextEntry::make('organization.name')->label('الجهة'),
                TextEntry::make('period_start')->label('من')->date(),
                TextEntry::make('period_end')->label('إلى')->date(),
                TextEntry::make('status')->label('الحالة'),
                TextEntry::make('total_gross_amount')->label('إجمالي المبيعات')->formatStateUsing(fn ($state) => $state !== null ? number_format((float) $state) . ' IQD' : '—'),
                TextEntry::make('total_partner_share')->label('حصة الشريك')->formatStateUsing(fn ($state) => $state !== null ? number_format((float) $state) . ' IQD' : '—'),
                TextEntry::make('total_organization_share')->label('حصة الجهة')->formatStateUsing(fn ($state) => $state !== null ? number_format((float) $state) . ' IQD' : '—'),
                TextEntry::make('total_platform_share')->label('حصة المنصّة')->formatStateUsing(fn ($state) => $state !== null ? number_format((float) $state) . ' IQD' : '—'),
            ])->columns(2),

            Section::make('القيود المندرجة')->schema([
                RepeatableEntry::make('reconciliationEntries')
                    ->schema([
                        TextEntry::make('ledgerEntry.posted_at')->label('تاريخ')->dateTime('Y-m-d H:i'),
                        TextEntry::make('ledgerEntry.account_code')->label('حساب'),
                        TextEntry::make('ledgerEntry.entry_type')->label('نوع'),
                        TextEntry::make('ledgerEntry.amount')->label('مبلغ')->formatStateUsing(fn ($state) => $state !== null ? number_format((float) $state) . ' IQD' : '—'),
                        TextEntry::make('ledgerEntry.description')->label('الوصف'),
                        TextEntry::make('ledgerEntry.invoice.number')->label('فاتورة')->placeholder('—'),
                    ])->columns(6)
            ])->collapsible(),
        ]);
    }
}
