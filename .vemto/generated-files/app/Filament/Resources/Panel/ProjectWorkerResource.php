<?php

namespace App\Filament\Resources\Panel;

use Filament\Forms;
use Filament\Tables;
use Livewire\Component;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\ProjectWorker;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Forms\Components\DateTimePicker;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Panel\ProjectWorkerResource\Pages;
use App\Filament\Resources\Panel\ProjectWorkerResource\RelationManagers;

class ProjectWorkerResource extends Resource
{
    protected static ?string $model = ProjectWorker::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Admin';

    public static function getModelLabel(): string
    {
        return __('crud.projectWorkers.itemTitle');
    }

    public static function getPluralModelLabel(): string
    {
        return __('crud.projectWorkers.collectionTitle');
    }

    public static function getNavigationLabel(): string
    {
        return __('crud.projectWorkers.collectionTitle');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->schema([
                Grid::make(['default' => 1])->schema([
                    Select::make('project_id')
                        ->label('Project')
                        ->required()
                        ->relationship('project', 'name')
                        ->searchable()
                        ->preload()
                        ->native(false),

                    Select::make('user_id')
                        ->label('User')
                        ->required()
                        ->relationship('user', 'name')
                        ->searchable()
                        ->preload()
                        ->native(false),

                    TextInput::make('role')
                        ->label(__('crud.projectWorkers.inputs.role.label'))
                        ->required()
                        ->string(),

                    DatePicker::make('assigned_at')
                        ->label(
                            __('crud.projectWorkers.inputs.assigned_at.label')
                        )
                        ->rules(['date'])
                        ->required()
                        ->native(false),

                    Checkbox::make('active')
                        ->label(__('crud.projectWorkers.inputs.active.label'))
                        ->rules(['boolean'])
                        ->required()
                        ->inline(),

                    RichEditor::make('notes')
                        ->label(__('crud.projectWorkers.inputs.notes.label'))
                        ->required()
                        ->string()
                        ->fileAttachmentsVisibility('public'),

                    DateTimePicker::make('deleted_at')
                        ->label(
                            __('crud.projectWorkers.inputs.deleted_at.label')
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
                TextColumn::make('project.name')->label('Project'),

                TextColumn::make('user.name')->label('User'),

                TextColumn::make('role')->label(
                    __('crud.projectWorkers.inputs.role.label')
                ),

                TextColumn::make('assigned_at')
                    ->label(__('crud.projectWorkers.inputs.assigned_at.label'))
                    ->since(),

                CheckboxColumn::make('active')->label(
                    __('crud.projectWorkers.inputs.active.label')
                ),

                TextColumn::make('notes')
                    ->label(__('crud.projectWorkers.inputs.notes.label'))
                    ->limit(255),

                TextColumn::make('deleted_at')
                    ->label(__('crud.projectWorkers.inputs.deleted_at.label'))
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
            'index' => Pages\ListProjectWorkers::route('/'),
            'create' => Pages\CreateProjectWorker::route('/create'),
            'view' => Pages\ViewProjectWorker::route('/{record}'),
            'edit' => Pages\EditProjectWorker::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
    }
}
