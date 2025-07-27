<?php

namespace App\Filament\Resources\Panel;

use Filament\Forms;
use Filament\Tables;
use Livewire\Component;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\ProfessionalRecord;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\DateTimePicker;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Panel\ProfessionalRecordResource\Pages;
use App\Filament\Resources\Panel\ProfessionalRecordResource\RelationManagers;

class ProfessionalRecordResource extends Resource
{
    protected static ?string $model = ProfessionalRecord::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Admin';

    public static function getModelLabel(): string
    {
        return __('crud.professionalRecords.itemTitle');
    }

    public static function getPluralModelLabel(): string
    {
        return __('crud.professionalRecords.collectionTitle');
    }

    public static function getNavigationLabel(): string
    {
        return __('crud.professionalRecords.collectionTitle');
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

                    TextInput::make('title')
                        ->label(
                            __('crud.professionalRecords.inputs.title.label')
                        )
                        ->required()
                        ->string(),

                    TextInput::make('organization')
                        ->label(
                            __(
                                'crud.professionalRecords.inputs.organization.label'
                            )
                        )
                        ->required()
                        ->string(),

                    DatePicker::make('start_date')
                        ->label(
                            __(
                                'crud.professionalRecords.inputs.start_date.label'
                            )
                        )
                        ->rules(['date'])
                        ->required()
                        ->native(false),

                    DatePicker::make('end_date')
                        ->label(
                            __('crud.professionalRecords.inputs.end_date.label')
                        )
                        ->rules(['date'])
                        ->required()
                        ->native(false),

                    RichEditor::make('details')
                        ->label(
                            __('crud.professionalRecords.inputs.details.label')
                        )
                        ->required()
                        ->string()
                        ->fileAttachmentsVisibility('public'),

                    DateTimePicker::make('deleted_at')
                        ->label(
                            __(
                                'crud.professionalRecords.inputs.deleted_at.label'
                            )
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
                TextColumn::make('user.name')->label('User'),

                TextColumn::make('title')->label(
                    __('crud.professionalRecords.inputs.title.label')
                ),

                TextColumn::make('organization')->label(
                    __('crud.professionalRecords.inputs.organization.label')
                ),

                TextColumn::make('start_date')
                    ->label(
                        __('crud.professionalRecords.inputs.start_date.label')
                    )
                    ->since(),

                TextColumn::make('end_date')
                    ->label(
                        __('crud.professionalRecords.inputs.end_date.label')
                    )
                    ->since(),

                TextColumn::make('details')
                    ->label(__('crud.professionalRecords.inputs.details.label'))
                    ->limit(255),

                TextColumn::make('deleted_at')
                    ->label(
                        __('crud.professionalRecords.inputs.deleted_at.label')
                    )
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
            'index' => Pages\ListProfessionalRecords::route('/'),
            'create' => Pages\CreateProfessionalRecord::route('/create'),
            'view' => Pages\ViewProfessionalRecord::route('/{record}'),
            'edit' => Pages\EditProfessionalRecord::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
    }
}
