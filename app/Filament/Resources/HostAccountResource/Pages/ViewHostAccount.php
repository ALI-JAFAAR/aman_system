<?php

namespace App\Filament\Resources\HostAccountResource\Pages;

use App\Filament\Resources\HostAccountResource;
use App\Models\LedgerEntry;
use App\Models\UserOffering;
use App\Services\AffiliationPostingService as COA;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;

class ViewHostAccount extends ViewRecord
{
    protected static string $resource = HostAccountResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make('معلومات الجهة')->schema([
                TextEntry::make('name')->label('الجهة'),
                TextEntry::make('type')->label('النوع'),
            ])->columns(2),

            Section::make('الحركة المحاسبية (2200)')->schema([
                RepeatableEntry::make('entries')
                    ->state(function ($record) {
                        // $record = Organization (جهة مضيفة)
                        return LedgerEntry::query()
                            ->select([
                                'ledger_entries.id',
                                'ledger_entries.posted_at',
                                'ledger_entries.description',
                                'ledger_entries.entry_type',
                                'ledger_entries.amount',
                                'invoices.number as invoice_number',
                            ])
                            ->leftJoin('invoices', 'invoices.id', '=', 'ledger_entries.invoice_id')
                            ->join('user_offerings as uo', function ($j) {
                                $j->on('ledger_entries.reference_id', '=', 'uo.id')
                                    ->where('ledger_entries.reference_type', '=', UserOffering::class);
                            })
                            ->join('invoice_items as ii', function ($j) {
                                $j->on('ii.invoice_id', '=', 'ledger_entries.invoice_id')
                                    ->on('ii.reference_id', '=', 'ledger_entries.reference_id')
                                    ->where('ii.item_type', '=', 'offering');
                            })
                            ->where('ii.organization_id', $record->id)
                            ->where('ledger_entries.account_code', COA::ACC_PAY_HOST)
                            ->orderByDesc('ledger_entries.id')
                            ->limit(300)
                            ->get()
                            ->map(fn ($e) => [
                                'date'     => optional($e->posted_at)->format('Y-m-d H:i'),
                                'invoice'  => $e->invoice_number ?? '—',
                                'desc'     => $e->description,
                                'debit'    => $e->entry_type === 'debit'  ? (float)$e->amount : 0,
                                'credit'   => $e->entry_type === 'credit' ? (float)$e->amount : 0,
                            ])->all();
                    })
                    ->schema([
                        TextEntry::make('date')->label('التاريخ'),
                        TextEntry::make('invoice')->label('الفاتورة'),
                        TextEntry::make('desc')->label('الوصف'),
                        TextEntry::make('debit')->label('مدين')->formatStateUsing(fn($v)=>$v?number_format($v):'—'),
                        TextEntry::make('credit')->label('دائن')->formatStateUsing(fn($v)=>$v?number_format($v):'—'),
                    ])->columns(5),
            ])->collapsible(),
        ]);
    }
}
