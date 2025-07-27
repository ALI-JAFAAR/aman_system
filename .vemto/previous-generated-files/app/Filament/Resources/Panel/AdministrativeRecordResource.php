<?php

namespace App\Filament\Resources\Panel;

use Filament\Forms;
use Filament\Tables;
use Livewire\Component;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use App\Models\AdministrativeRecord;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Panel\AdministrativeRecordResource\Pages;
use App\Filament\Resources\Panel\AdministrativeRecordResource\RelationManagers;

class AdministrativeRecordResource extends Resource
{
    protected static ?string $model = AdministrativeRecord::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Admin';

    public static function getModelLabel(): string
    {
        return __('crud.administrativeRecords.itemTitle');
    }

    public static function getPluralModelLabel(): string
    {
        return __('crud.administrativeRecords.collectionTitle');
    }

    public static function getNavigationLabel(): string
    {
        return __('crud.administrativeRecords.collectionTitle');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->schema([
                Grid::make(['default' => 1])->schema([
                    Select::make('user_id')
                        ->label('User')
                        ->required()
                        ->relationship('user', 'name')
                        ->searchable()
                        ->preload()
                        ->native(false),

                    Select::make('record_type')
                        ->label(
                            __(
                                'crud.administrativeRecords.inputs.record_type.label'
                            )
                        )
                        ->required()
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->options([
                            'identity' => 'Identity',
                            'certificate' => 'Certificate',
                            'license' => 'License',
                            'warning' => 'Warning',
                            'other' => 'Other',
                        ]),

                    TextInput::make('title')
                        ->label(
                            __('crud.administrativeRecords.inputs.title.label')
                        )
                        ->required()
                        ->string(),

                    RichEditor::make('description')
                        ->label(
                            __(
                                'crud.administrativeRecords.inputs.description.label'
                            )
                        )
                        ->required()
                        ->string()
                        ->fileAttachmentsVisibility('public'),

                    DatePicker::make('record_date')
                        ->label(
                            __(
                                'crud.administrativeRecords.inputs.record_date.label'
                            )
                        )
                        ->rules(['date'])
                        ->required()
                        ->native(false),

                    DatePicker::make('expiry_date')
                        ->label(
                            __(
                                'crud.administrativeRecords.inputs.expiry_date.label'
                            )
                        )
                        ->rules(['date'])
                        ->required()
                        ->native(false),

                    RichEditor::make('record_data')
                        ->label(
                            __(
                                'crud.administrativeRecords.inputs.record_data.label'
                            )
                        )
                        ->nullable()
                        ->fileAttachmentsVisibility('public'),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->poll('60s')
            ->columns([
                TextColumn::make('user.name')->label('User'),

                TextColumn::make('record_type')->label(
                    __('crud.administrativeRecords.inputs.record_type.label')
                ),

                TextColumn::make('title')->label(
                    __('crud.administrativeRecords.inputs.title.label')
                ),

                TextColumn::make('description')
                    ->label(
                        __(
                            'crud.administrativeRecords.inputs.description.label'
                        )
                    )
                    ->limit(255),

                TextColumn::make('record_date')
                    ->label(
                        __(
                            'crud.administrativeRecords.inputs.record_date.label'
                        )
                    )
                    ->since(),

                TextColumn::make('expiry_date')
                    ->label(
                        __(
                            'crud.administrativeRecords.inputs.expiry_date.label'
                        )
                    )
                    ->since(),

                TextColumn::make('record_data')
                    ->label(
                        __(
                            'crud.administrativeRecords.inputs.record_data.label'
                        )
                    )
                    ->limit(255),
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
            'index' => Pages\ListAdministrativeRecords::route('/'),
            'create' => Pages\CreateAdministrativeRecord::route('/create'),
            'view' => Pages\ViewAdministrativeRecord::route('/{record}'),
            'edit' => Pages\EditAdministrativeRecord::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
    }
}
