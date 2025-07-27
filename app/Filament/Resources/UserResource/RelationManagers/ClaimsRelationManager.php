<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Models\Claim;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Table;

class ClaimsRelationManager extends RelationManager
{
    protected static string $relationship = 'claims';
    protected static ?string $recordTitleAttribute = 'type';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')->label('نوع المطالبة'),
                BadgeColumn::make('status')
                    ->label('حالة المطالبة')
//                    ->enum([
//                        'pending'  => 'في الانتظار',
//                        'approved' => 'مقبولة',
//                        'rejected' => 'مرفوضة',
//                    ])
                ,
                TextColumn::make('submitted_at')->label('تاريخ التقديم')->dateTime('Y-m-d'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
