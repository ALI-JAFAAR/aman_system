<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Models\Transaction;
use Filament\Resources\RelationManagers\RelationManager;

use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TransactionsRelationManager extends RelationManager
{
    protected static string $relationship = 'transactions';
    protected static ?string $recordTitleAttribute = 'transaction_type';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('transaction_type')->label('النوع'),
                TextColumn::make('amount')->label('المبلغ'),
                TextColumn::make('created_at')->label('تاريخ المعاملة')->dateTime('Y-m-d H:i'),
            ])
            ->headerActions([])
            ->actions([]);
    }
}
