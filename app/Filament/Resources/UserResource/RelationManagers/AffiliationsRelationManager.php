<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Models\UserAffiliation;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Table;

class AffiliationsRelationManager extends RelationManager
{
    protected static string $relationship = 'userAffiliations';
    protected static ?string $recordTitleAttribute = 'organization.name';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('organization.name')->label('الجهة'),
                BadgeColumn::make('status')
                    ->label('الحالة')
//                    ->enum([
//                        'pending'  => 'في الانتظار',
//                        'approved' => 'مقبول',
//                        'rejected' => 'مرفوض',
//                    ])
                ,
                TextColumn::make('joined_at')->label('تاريخ الطلب')->dateTime('Y-m-d H:i'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
