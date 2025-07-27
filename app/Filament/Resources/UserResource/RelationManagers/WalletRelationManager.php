<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Models\Wallet;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WalletRelationManager extends RelationManager
{
    protected static string $relationship = 'wallets';
    protected static ?string $recordTitleAttribute = 'balance';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('balance')->label('الرصيد'),
            ])
            ->headerActions([]) // لا إنشاء أو حذف لمحفظة من هنا
            ->actions([]);
    }
}
