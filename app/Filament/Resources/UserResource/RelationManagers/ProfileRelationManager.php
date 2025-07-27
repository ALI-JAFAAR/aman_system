<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Models\UserProfile;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProfileRelationManager extends RelationManager {
    protected static string $relationship = 'userProfiles';
    protected static ?string $recordTitleAttribute = 'name';

    public  function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('الاسم الأول'),
                TextColumn::make('mother_name')->label('اسم الأم'),
                TextColumn::make('national_id')->label('رقم الهوية'),
                TextColumn::make('phone')->label('الهاتف'),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ])->actions([
                // عرض زر التعديل فقط إذا كان هناك سجلٌ موجود
                Tables\Actions\EditAction::make()
                    ->visible(fn ($record) => $record !== null),
            ]);
    }
}
