<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AccountingPeriodResource\Pages;
use App\Models\AccountingPeriod;
use App\Models\LedgerEntry;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class AccountingPeriodResource extends Resource
{
    protected static ?string $model = AccountingPeriod::class;
    protected static ?string $navigationGroup = 'الحسابات';
    protected static ?string $navigationLabel = 'الفترات المحاسبية';
    protected static ?string $navigationIcon  = 'heroicon-o-calendar-days';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->label('الاسم')->required(),
            Forms\Components\DatePicker::make('start_date')->label('من')->required(),
            Forms\Components\DatePicker::make('end_date')->label('إلى')->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('الفترة'),
                Tables\Columns\TextColumn::make('start_date')->label('من')->date(),
                Tables\Columns\TextColumn::make('end_date')->label('إلى')->date(),
                Tables\Columns\IconColumn::make('is_closed')->label('مقفلة؟')->boolean(),
                Tables\Columns\TextColumn::make('closed_at')->label('وقت الإقفال')->dateTime(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('close')
                    ->label('إقفال الفترة')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => !$record->is_closed)
                    ->action(function (AccountingPeriod $record) {
                        DB::transaction(function () use ($record) {
                            // أقفل كل القيود داخل المدة
                            LedgerEntry::whereDate('posted_at','>=',$record->start_date)
                                ->whereDate('posted_at','<=',$record->end_date)
                                ->update(['is_locked' => true]);

                            $record->is_closed = true;
                            $record->closed_by = optional(auth()->user()?->employee)->id;
                            $record->closed_at = now();
                            $record->save();
                        });
                    }),
                Tables\Actions\Action::make('reopen')
                    ->label('فتح الفترة')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->is_closed)
                    ->action(function (AccountingPeriod $record) {
                        DB::transaction(function () use ($record) {
                            LedgerEntry::whereDate('posted_at','>=',$record->start_date)
                                ->whereDate('posted_at','<=',$record->end_date)
                                ->update(['is_locked' => false]);

                            $record->is_closed = false;
                            $record->closed_by = null;
                            $record->closed_at = null;
                            $record->save();
                        });
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAccountingPeriods::route('/'),
        ];
    }
}
