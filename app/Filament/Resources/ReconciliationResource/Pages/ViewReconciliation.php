<?php
namespace App\Filament\Resources\ReconciliationResource\Pages;

use App\Filament\Resources\ReconciliationResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Actions\Action;
use App\Services\ReconciliationBuilder;

class ViewReconciliation extends ViewRecord{

    protected static string $resource = ReconciliationResource::class;

    protected function getHeaderActions(): array{
        return [
            Action::make('finalize')
                ->label('اعتماد وإقفال')
                ->requiresConfirmation()
                ->action(function () {
                    app(ReconciliationBuilder::class)->finalize($this->record);
//                    $this->notify('success', 'تم اعتماد التسوية وإقفال القيود وتحديث محفظة الجهة.');
                    $this->refreshFormData();
                })
                ->visible(fn () => $this->record->status === 'draft'),
        ];
    }
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


            Section::make('القيود المُدرجة')->schema([
                RepeatableEntry::make('entries_list')
                    ->label('')
                    ->state(function ($record) {
                        return $record->reconciliationEntries()
                            ->with(['ledgerEntry.invoice'])
                            ->latest('id')
                            ->get()
                            ->map(function ($re) {
                                $le = $re->ledgerEntry;
                                return [
                                    'posted_at'   => optional($le->posted_at)->format('Y-m-d'),
                                    'account'     => $le->account_code,
                                    'type'        => $le->entry_type,
                                    'amount'      => (float) $le->amount,
                                    'description' => $le->description,
                                    'invoice_no'  => optional($le->invoice)->number,
                                ];
                            })->all();
                    })
                    ->schema([
                        TextEntry::make('posted_at')->label('التاريخ'),
                        TextEntry::make('invoice_no')->label('الفاتورة')->placeholder('—'),
                        TextEntry::make('account')->label('الحساب'),
                        TextEntry::make('type')->label('النوع')->badge()
                            ->colors(['success' => 'debit', 'danger' => 'credit']),
                        TextEntry::make('amount')->label('المبلغ')
                            ->formatStateUsing(fn ($state) => number_format((float) $state) . ' IQD'),
                        TextEntry::make('description')->label('الوصف')->columnSpanFull(),
                    ])
                    ->visible(fn ($state) => is_array($state) && count($state) > 0),
            ])->collapsible()
        ]);
    }
}
