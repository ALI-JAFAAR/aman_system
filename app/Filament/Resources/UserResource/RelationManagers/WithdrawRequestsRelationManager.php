<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Models\WithdrawRequest;
use Filament\Resources\RelationManagers\RelationManager;

use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WithdrawRequestsRelationManager extends RelationManager
{
    protected static string $relationship = 'withdrawRequests';
    protected static ?string $recordTitleAttribute = 'id';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('amount')->label('المبلغ')->currency('IQD'),
                TextColumn::make('status')
                    ->badge()
                    ->label('الحالة')
//                    ->enum([
//                        'pending'  => 'في الانتظار',
//                        'approved' => 'موافق',
//                        'rejected' => 'مرفوض',
//                    ])
                ,
                TextColumn::make('requested_at')->label('تاريخ الطلب')->dateTime('Y-m-d H:i'),
                TextColumn::make('approved_at')->label('تاريخ الموافقة')->dateTime('Y-m-d H:i')->placeholder('-'),
                TextColumn::make('executed_at')->label('تاريخ التنفيذ')->dateTime('Y-m-d H:i')->placeholder('-'),
            ])
            ->headerActions([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }
}
