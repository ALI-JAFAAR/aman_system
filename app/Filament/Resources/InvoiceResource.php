<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages\ViewInvoice;
use App\Models\{Invoice, User, UserProfile, InvoiceItem, Payment, LedgerEntry, Organization, PartnerOffering, Package, UserAffiliation, UserOffering};
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextColumn;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationGroup = 'الفوترة والمالية';
    protected static ?string $navigationLabel = 'الفواتير';
    protected static ?int    $navigationSort  = 10;
    protected static ?string $navigationIcon  = 'heroicon-o-document-currency-dollar';


    protected static ?string $pluralLabel     = 'طلبات الانتساب';
    protected static ?string $modelLabel      = 'طلب انتساب';

    public static function getPages(): array
    {
        return [
            'index' => InvoiceResource\Pages\ListInvoices::route('/'),
            'view'  => ViewInvoice::route('/{record}'),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Invoice::query()->latest('id'))
            ->columns([
                TextColumn::make('number')->label('رقم الفاتورة')->searchable()->sortable(),
                TextColumn::make('user_name')
                    ->label('المتقدّم')
                    ->sortable()
                    ->searchable()
                    ->getStateUsing(fn ($record) => optional($record->user)->name ?? User::where('id', $record->user_id)->value('name')),
                TextColumn::make('issued_at')->label('تاريخ الإصدار')->dateTime()->sortable(),
                TextColumn::make('subtotal')->label('الإجمالي قبل الخصم')->money('IQD', true)->sortable(),
                TextColumn::make('discount_amount')->label('الخصم')->money('IQD', true)->sortable(),
                TextColumn::make('total')->label('الإجمالي')->money('IQD', true)->sortable(),
                TextColumn::make('paid')->label('المدفوع')->money('IQD', true)->sortable(),
                TextColumn::make('balance')->label('المتبقي')->money('IQD', true)->sortable(),
                TextColumn::make('status')->label('الحالة')->badge()
                    ->colors([
                        'success' => 'paid',
                        'warning' => 'partial',
                        'danger'  => 'unpaid',
                    ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('الحالة')->options([
                        'paid'    => 'مدفوعة',
                        'partial' => 'مدفوع جزئياً',
                        'unpaid'  => 'غير مدفوعة',
                    ]),
                Tables\Filters\Filter::make('issued_range')->label('نطاق التاريخ')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('from')->label('من'),
                        \Filament\Forms\Components\DatePicker::make('to')->label('إلى'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['from'] ?? null, fn ($q, $d) => $q->whereDate('issued_at', '>=', $d))
                            ->when($data['to']   ?? null, fn ($q, $d) => $q->whereDate('issued_at', '<=', $d));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('عرض التفاصيل'),
                Tables\Actions\Action::make('print')
                    ->label('طباعة')
                    ->icon('heroicon-o-printer')
                    ->url(fn ($record) => route('invoices.print', $record))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([]);
    }
}
