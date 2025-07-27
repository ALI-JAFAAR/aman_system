<?php

namespace App\Filament\Resources\Panel;

use Filament\Forms;
use Filament\Tables;
use Livewire\Component;
use App\Models\Project;
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
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Panel\ProjectResource\Pages;
use App\Filament\Resources\Panel\ProjectResource\RelationManagers;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Admin';

    public static function getModelLabel(): string
    {
        return __('crud.projects.itemTitle');
    }

    public static function getPluralModelLabel(): string
    {
        return __('crud.projects.collectionTitle');
    }

    public static function getNavigationLabel(): string
    {
        return __('crud.projects.collectionTitle');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->schema([
                Grid::make(['default' => 1])->schema([
                    Select::make('owner_id')
                        ->label('User')
                        ->required()
                        ->relationship('user', 'name')
                        ->searchable()
                        ->preload()
                        ->native(false),

                    Select::make('organization_id')
                        ->label('Organization')
                        ->required()
                        ->relationship('organization', 'name')
                        ->searchable()
                        ->preload()
                        ->native(false),

                    TextInput::make('name')
                        ->label(__('crud.projects.inputs.name.label'))
                        ->required()
                        ->string(),

                    RichEditor::make('description')
                        ->label(__('crud.projects.inputs.description.label'))
                        ->required()
                        ->string()
                        ->fileAttachmentsVisibility('public'),

                    RichEditor::make('location')
                        ->label(__('crud.projects.inputs.location.label'))
                        ->required()
                        ->string()
                        ->fileAttachmentsVisibility('public'),

                    DatePicker::make('start_date')
                        ->label(__('crud.projects.inputs.start_date.label'))
                        ->rules(['date'])
                        ->required()
                        ->native(false),

                    DatePicker::make('end_date')
                        ->label(__('crud.projects.inputs.end_date.label'))
                        ->rules(['date'])
                        ->required()
                        ->native(false),

                    Select::make('status')
                        ->label(__('crud.projects.inputs.status.label'))
                        ->required()
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->options([
                            'pending' => 'Pending',
                            'active' => 'Active',
                            'completed' => 'Completed',
                            'cancelled' => 'Cancelled',
                        ]),

                    DateTimePicker::make('deleted_at')
                        ->label(__('crud.projects.inputs.deleted_at.label'))
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

                TextColumn::make('organization.name')->label('Organization'),

                TextColumn::make('name')->label(
                    __('crud.projects.inputs.name.label')
                ),

                TextColumn::make('description')
                    ->label(__('crud.projects.inputs.description.label'))
                    ->limit(255),

                TextColumn::make('location')
                    ->label(__('crud.projects.inputs.location.label'))
                    ->limit(255),

                TextColumn::make('start_date')
                    ->label(__('crud.projects.inputs.start_date.label'))
                    ->since(),

                TextColumn::make('end_date')
                    ->label(__('crud.projects.inputs.end_date.label'))
                    ->since(),

                TextColumn::make('status')->label(
                    __('crud.projects.inputs.status.label')
                ),

                TextColumn::make('deleted_at')
                    ->label(__('crud.projects.inputs.deleted_at.label'))
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
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'view' => Pages\ViewProject::route('/{record}'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
    }
}
