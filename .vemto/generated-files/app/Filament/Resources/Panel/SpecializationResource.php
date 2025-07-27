<?php

namespace App\Filament\Resources\Panel;

use Filament\Forms;
use Filament\Tables;
use Livewire\Component;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Specialization;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\DateTimePicker;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Panel\SpecializationResource\Pages;
use App\Filament\Resources\Panel\SpecializationResource\RelationManagers;

class SpecializationResource extends Resource
{
    protected static ?string $model = Specialization::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Admin';

    public static function getModelLabel(): string
    {
        return __('crud.specializations.itemTitle');
    }

    public static function getPluralModelLabel(): string
    {
        return __('crud.specializations.collectionTitle');
    }

    public static function getNavigationLabel(): string
    {
        return __('crud.specializations.collectionTitle');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->schema([
                Grid::make(['default' => 1])->schema([
                    Select::make('profession_id')
                        ->label('Profession')
                        ->required()
                        ->relationship('profession', 'name')
                        ->searchable()
                        ->preload()
                        ->native(false),

                    TextInput::make('code')
                        ->label(__('crud.specializations.inputs.code.label'))
                        ->required()
                        ->string(),

                    TextInput::make('name')
                        ->label(__('crud.specializations.inputs.name.label'))
                        ->required()
                        ->string(),

                    RichEditor::make('description')
                        ->label(
                            __('crud.specializations.inputs.description.label')
                        )
                        ->required()
                        ->string()
                        ->fileAttachmentsVisibility('public'),

                    DateTimePicker::make('deleted_at')
                        ->label(
                            __('crud.specializations.inputs.deleted_at.label')
                        )
                        ->rules(['date'])
                        ->nullable()
                        ->native(false),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->poll('60s')
            ->columns([
                TextColumn::make('profession.name')->label('Profession'),

                TextColumn::make('code')->label(
                    __('crud.specializations.inputs.code.label')
                ),

                TextColumn::make('name')->label(
                    __('crud.specializations.inputs.name.label')
                ),

                TextColumn::make('description')
                    ->label(__('crud.specializations.inputs.description.label'))
                    ->limit(255),

                TextColumn::make('deleted_at')
                    ->label(__('crud.specializations.inputs.deleted_at.label'))
                    ->since(),
            ])
            ->filters([Tables\Filters\TrashedFilter::make()])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSpecializations::route('/'),
            'create' => Pages\CreateSpecialization::route('/create'),
            'view' => Pages\ViewSpecialization::route('/{record}'),
            'edit' => Pages\EditSpecialization::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
    }
}
