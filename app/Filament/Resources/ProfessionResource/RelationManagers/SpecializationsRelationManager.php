<?php

namespace App\Filament\Resources\ProfessionResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Tables;
use Filament\Tables\Table;

class SpecializationsRelationManager extends RelationManager
{
    protected static string $relationship = 'specializations';
    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form{
        return $form->schema([
            TextInput::make('name')->label('الاسم')->required()->maxLength(255),
            TextInput::make('code')->label('الكود')->maxLength(50),
            Toggle::make('is_active')->label('فعّال')->default(true),
        ]);
    }

    public  function table(Table $table): Table{
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->label('الاسم')->searchable(),
            Tables\Columns\TextColumn::make('code')->label('الكود'),
            Tables\Columns\IconColumn::make('is_active')->boolean()->label('فعّال'),
            Tables\Columns\TextColumn::make('created_at')->since()->label('أُنشئ'),
        ])->headerActions([
            Tables\Actions\CreateAction::make(),
        ])->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ]);
    }
}
