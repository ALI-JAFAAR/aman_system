<?php

namespace App\Filament\Resources\Panel;

use Filament\Forms;
use Filament\Tables;
use Livewire\Component;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\UserService;
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
use App\Filament\Resources\Panel\UserServiceResource\Pages;
use App\Filament\Resources\Panel\UserServiceResource\RelationManagers;

class UserServiceResource extends Resource
{
    protected static ?string $model = UserService::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Admin';

    public static function getModelLabel(): string
    {
        return __('crud.userServices.itemTitle');
    }

    public static function getPluralModelLabel(): string
    {
        return __('crud.userServices.collectionTitle');
    }

    public static function getNavigationLabel(): string
    {
        return __('crud.userServices.collectionTitle');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->schema([
                Grid::make(['default' => 1])->schema([
                    RichEditor::make('form_data')
                        ->label(__('crud.userServices.inputs.form_data.label'))
                        ->required()
                        ->fileAttachmentsVisibility('public'),

                    RichEditor::make('status')
                        ->label(__('crud.userServices.inputs.status.label'))
                        ->required()
                        ->fileAttachmentsVisibility('public'),

                    RichEditor::make('response_data')
                        ->label(
                            __('crud.userServices.inputs.response_data.label')
                        )
                        ->required()
                        ->fileAttachmentsVisibility('public'),

                    TextInput::make('submitted_at')
                        ->label(
                            __('crud.userServices.inputs.submitted_at.label')
                        )
                        ->required()
                        ->string(),

                    TextInput::make('processed_at')
                        ->label(
                            __('crud.userServices.inputs.processed_at.label')
                        )
                        ->required()
                        ->string(),

                    TextInput::make('processed_by')
                        ->label(
                            __('crud.userServices.inputs.processed_by.label')
                        )
                        ->required()
                        ->numeric()
                        ->step(1),

                    RichEditor::make('notes')
                        ->label(__('crud.userServices.inputs.notes.label'))
                        ->required()
                        ->string()
                        ->fileAttachmentsVisibility('public'),

                    Select::make('user_id')
                        ->label('User')
                        ->required()
                        ->relationship('user', 'name')
                        ->searchable()
                        ->preload()
                        ->native(false),

                    Select::make('service_id')
                        ->label('Service')
                        ->required()
                        ->relationship('service', 'name')
                        ->searchable()
                        ->preload()
                        ->native(false),

                    Select::make('employee_id')
                        ->label('Employee')
                        ->required()
                        ->relationship('employee', 'job_title')
                        ->searchable()
                        ->preload()
                        ->native(false),

                    DateTimePicker::make('deleted_at')
                        ->label(__('crud.userServices.inputs.deleted_at.label'))
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
                TextColumn::make('form_data')
                    ->label(__('crud.userServices.inputs.form_data.label'))
                    ->limit(255),

                TextColumn::make('status')
                    ->label(__('crud.userServices.inputs.status.label'))
                    ->limit(255),

                TextColumn::make('response_data')
                    ->label(__('crud.userServices.inputs.response_data.label'))
                    ->limit(255),

                TextColumn::make('submitted_at')->label(
                    __('crud.userServices.inputs.submitted_at.label')
                ),

                TextColumn::make('processed_at')->label(
                    __('crud.userServices.inputs.processed_at.label')
                ),

                TextColumn::make('processed_by')->label(
                    __('crud.userServices.inputs.processed_by.label')
                ),

                TextColumn::make('notes')
                    ->label(__('crud.userServices.inputs.notes.label'))
                    ->limit(255),

                TextColumn::make('user.name')->label('User'),

                TextColumn::make('service.name')->label('Service'),

                TextColumn::make('employee.job_title')->label('Employee'),

                TextColumn::make('deleted_at')
                    ->label(__('crud.userServices.inputs.deleted_at.label'))
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
            'index' => Pages\ListUserServices::route('/'),
            'create' => Pages\CreateUserService::route('/create'),
            'view' => Pages\ViewUserService::route('/{record}'),
            'edit' => Pages\EditUserService::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
    }
}
