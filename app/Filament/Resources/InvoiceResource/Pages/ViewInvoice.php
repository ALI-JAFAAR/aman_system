<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use App\Models\Invoice;

class ViewInvoice extends ViewRecord
{
    protected static string $resource = InvoiceResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        /** @var \App\Models\Invoice $record */
        $record = $this->getRecord();
        $record->loadMissing(['user.userProfiles']);

        return $infolist->schema([

            Section::make('بيانات الفاتورة')
                ->schema([
                    TextEntry::make('number')->label('رقم الفاتورة'),

                    TextEntry::make('issued_at')
                        ->label('تاريخ الإصدار')
                        ->dateTime('Y-m-d H:i'),

                    TextEntry::make('status')->label('الحالة'),

                    TextEntry::make('subtotal')
                        ->label('الإجمالي قبل الخصم')
                        ->formatStateUsing(fn ($state) => number_format((float) ($state ?? 0)) . ' IQD'),

                    TextEntry::make('discount_amount')
                        ->label('الخصم')
                        ->formatStateUsing(fn ($state) => number_format((float) ($state ?? 0)) . ' IQD'),

                    TextEntry::make('total')
                        ->label('الإجمالي')
                        ->formatStateUsing(fn ($state) => number_format((float) ($state ?? 0)) . ' IQD'),

                    TextEntry::make('paid')
                        ->label('المسدد')
                        ->formatStateUsing(fn ($state) => number_format((float) ($state ?? 0)) . ' IQD'),

                    TextEntry::make('balance')
                        ->label('المتبقي')
                        ->formatStateUsing(fn ($state) => number_format((float) ($state ?? 0)) . ' IQD'),
                ])
                ->columns(4),

            Section::make('بيانات المنتسب')
                ->schema([
                    TextEntry::make('user.name')->label('الاسم')->placeholder('—'),
                    TextEntry::make('user.email')->label('البريد')->placeholder('—'),
                    TextEntry::make('user.userProfiles.0.phone')->label('الهاتف')->placeholder('—'),
                ])
                ->columns(3),

            Section::make('رسوم الانتساب')
                ->schema([
                    ViewEntry::make('affiliations_table')
                        ->view('invoices.partials.affiliations')
                        ->viewData(['invoice' => $record])
                        ->columnSpanFull(),
                ])
                ->collapsible(),

            Section::make('الباقات / العروض')
                ->schema([
                    ViewEntry::make('offerings_table')
                        ->view('invoices.partials.offerings')
                        ->viewData(['invoice' => $record])
                        ->columnSpanFull(),
                ])
                ->collapsible(),

            Section::make('الخدمات الإضافية')
                ->schema([
                    ViewEntry::make('services_table')
                        ->view('invoices.partials.services')
                        ->viewData(['invoice' => $record])
                        ->columnSpanFull(),
                ])
                ->collapsible(),

            Section::make('المدفوعات')
                ->schema([
                    ViewEntry::make('payments_table')
                        ->view('invoices.partials.payments')
                        ->viewData(['invoice' => $record])
                        ->columnSpanFull(),
                ])
                ->collapsible(),

            Section::make('قيود اليومية')
                ->schema([
                    ViewEntry::make('ledgers_table')
                        ->view('invoices.partials.ledgers')
                        ->viewData(['invoice' => $record])
                        ->columnSpanFull(),
                ])
                ->collapsible(),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('print')
                ->label('طباعة الفاتورة')
                ->url(fn (Invoice $record) => route('invoices.print', $record))
                ->openUrlInNewTab(),
        ];
    }
}
