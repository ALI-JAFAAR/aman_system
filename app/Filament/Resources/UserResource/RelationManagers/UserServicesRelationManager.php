<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Models\UserService;
use Filament\Resources\RelationManagers\RelationManager;

use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UserServicesRelationManager extends RelationManager
{
    protected static string $relationship = 'userServices';
    protected static ?string $recordTitleAttribute = 'service.code';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('service.name')->label('الخدمة'),
                TextColumn::make('status')
                    ->badge()
                    ->label('الحالة')
//                    ->enum([
//                        'pending'   => 'في الانتظار',
//                        'completed' => 'مكتمل',
//                    ])
                ,
                TextColumn::make('submitted_at')->label('تاريخ الإرسال')->dateTime('Y-m-d'),
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
