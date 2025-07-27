<?php

namespace App\Filament\Resources\Panel;

use Filament\Forms;
use Filament\Tables;
use Livewire\Component;
use App\Models\Vehicle;
use Filament\Forms\Form;
use Filament\Tables\Table;
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
use App\Filament\Resources\Panel\VehicleResource\Pages;
use App\Filament\Resources\Panel\VehicleResource\RelationManagers;

class VehicleResource extends Resource
{
    protected static ?string $model = Vehicle::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Admin';

    public static function getModelLabel(): string
    {
        return __('crud.vehicles.itemTitle');
    }

    public static function getPluralModelLabel(): string
    {
        return __('crud.vehicles.collectionTitle');
    }

    public static function getNavigationLabel(): string
    {
        return __('crud.vehicles.collectionTitle');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->schema([
                Grid::make(['default' => 1])->schema([
                    Select::make('user_service_id')
                        ->label('User Service')
                        ->required()
                        ->relationship('userService', 'submitted_at')
                        ->searchable()
                        ->preload()
                        ->native(false),

                    TextInput::make('plate_number')
                        ->label(__('crud.vehicles.inputs.plate_number.label'))
                        ->required()
                        ->string(),

                    TextInput::make('plate_code')
                        ->label(__('crud.vehicles.inputs.plate_code.label'))
                        ->required()
                        ->string(),

                    TextInput::make('model')
                        ->label(__('crud.vehicles.inputs.model.label'))
                        ->required()
                        ->string(),

                    RichEditor::make('owner_data')
                        ->label(__('crud.vehicles.inputs.owner_data.label'))
                        ->nullable()
                        ->fileAttachmentsVisibility('public'),

                    RichEditor::make('notes')
                        ->label(__('crud.vehicles.inputs.notes.label'))
                        ->required()
                        ->string()
                        ->fileAttachmentsVisibility('public'),

                    DateTimePicker::make('deleted_at')
                        ->label(__('crud.vehicles.inputs.deleted_at.label'))
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
                TextColumn::make('userService.submitted_at')->label(
                    'User Service'
                ),

                TextColumn::make('plate_number')->label(
                    __('crud.vehicles.inputs.plate_number.label')
                ),

                TextColumn::make('plate_code')->label(
                    __('crud.vehicles.inputs.plate_code.label')
                ),

                TextColumn::make('model')->label(
                    __('crud.vehicles.inputs.model.label')
                ),

                TextColumn::make('owner_data')
                    ->label(__('crud.vehicles.inputs.owner_data.label'))
                    ->limit(255),

                TextColumn::make('notes')
                    ->label(__('crud.vehicles.inputs.notes.label'))
                    ->limit(255),

                TextColumn::make('deleted_at')
                    ->label(__('crud.vehicles.inputs.deleted_at.label'))
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
            'index' => Pages\ListVehicles::route('/'),
            'create' => Pages\CreateVehicle::route('/create'),
            'view' => Pages\ViewVehicle::route('/{record}'),
            'edit' => Pages\EditVehicle::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
    }
}
