<?php

namespace App\Filament\Resources\Panel;

use Filament\Forms;
use Filament\Tables;
use Livewire\Component;
use App\Models\Service;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\DateTimePicker;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Panel\ServiceResource\Pages;
use App\Filament\Resources\Panel\ServiceResource\RelationManagers;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Admin';

    public static function getModelLabel(): string
    {
        return __('crud.services.itemTitle');
    }

    public static function getPluralModelLabel(): string
    {
        return __('crud.services.collectionTitle');
    }

    public static function getNavigationLabel(): string
    {
        return __('crud.services.collectionTitle');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->schema([
                Grid::make(['default' => 1])->schema([
                    TextInput::make('code')
                        ->label(__('crud.services.inputs.code.label'))
                        ->required()
                        ->string()
                        ->autofocus(),

                    TextInput::make('name')
                        ->label(__('crud.services.inputs.name.label'))
                        ->required()
                        ->string(),

                    RichEditor::make('description')
                        ->label(__('crud.services.inputs.description.label'))
                        ->required()
                        ->string()
                        ->fileAttachmentsVisibility('public'),

                    RichEditor::make('form_schema')
                        ->label(__('crud.services.inputs.form_schema.label'))
                        ->required()
                        ->fileAttachmentsVisibility('public'),

                    DateTimePicker::make('deleted_at')
                        ->label(__('crud.services.inputs.deleted_at.label'))
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
                TextColumn::make('code')->label(
                    __('crud.services.inputs.code.label')
                ),

                TextColumn::make('name')->label(
                    __('crud.services.inputs.name.label')
                ),

                TextColumn::make('description')
                    ->label(__('crud.services.inputs.description.label'))
                    ->limit(255),

                TextColumn::make('form_schema')
                    ->label(__('crud.services.inputs.form_schema.label'))
                    ->limit(255),

                TextColumn::make('deleted_at')
                    ->label(__('crud.services.inputs.deleted_at.label'))
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
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'view' => Pages\ViewService::route('/{record}'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
    }
}
