<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProfessionResource\Pages;
use App\Filament\Resources\ProfessionResource\RelationManagers\SpecializationsRelationManager;
use App\Models\Profession;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;

class ProfessionResource extends Resource
{
    protected static ?string $model = Profession::class;

    protected static ?string $navigationIcon  = 'heroicon-o-briefcase';
    protected static ?string $navigationLabel = 'المهن';
    protected static ?string $modelLabel      = 'مهنة';
    protected static ?string $pluralModelLabel= 'المهن';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')->label('الاسم')->required()->maxLength(255),
            TextInput::make('code')->label('الكود')->maxLength(50),
            Toggle::make('is_active')->label('فعّال')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('name')->label('الاسم')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('code')->label('الكود')->searchable(),
                Tables\Columns\TextColumn::make('specializations_count')->counts('specializations')->label('عدد الاختصاصات'),
                Tables\Columns\IconColumn::make('is_active')->boolean()->label('فعّال'),
                Tables\Columns\TextColumn::make('created_at')->since()->label('أُنشئ'),
            ])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()]);
    }

    public static function getRelations(): array
    {
        return [ SpecializationsRelationManager::class ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListProfessions::route('/'),
            'create' => Pages\CreateProfession::route('/create'),
            'edit'   => Pages\EditProfession::route('/{record}/edit'),
        ];
    }
}
