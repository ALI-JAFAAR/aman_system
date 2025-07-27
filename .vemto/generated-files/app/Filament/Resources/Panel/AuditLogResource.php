<?php

namespace App\Filament\Resources\Panel;

use Filament\Forms;
use Filament\Tables;
use Livewire\Component;
use Filament\Forms\Form;
use App\Models\AuditLog;
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
use App\Filament\Resources\Panel\AuditLogResource\Pages;
use App\Filament\Resources\Panel\AuditLogResource\RelationManagers;

class AuditLogResource extends Resource
{
    protected static ?string $model = AuditLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Admin';

    public static function getModelLabel(): string
    {
        return __('crud.auditLogs.itemTitle');
    }

    public static function getPluralModelLabel(): string
    {
        return __('crud.auditLogs.collectionTitle');
    }

    public static function getNavigationLabel(): string
    {
        return __('crud.auditLogs.collectionTitle');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->schema([
                Grid::make(['default' => 1])->schema([
                    TextInput::make('action')
                        ->label(__('crud.auditLogs.inputs.action.label'))
                        ->required()
                        ->string()
                        ->autofocus(),

                    Select::make('user_id')
                        ->label('User')
                        ->required()
                        ->relationship('user', 'name')
                        ->searchable()
                        ->preload()
                        ->native(false),

                    TextInput::make('model_type')
                        ->label(__('crud.auditLogs.inputs.model_type.label'))
                        ->required()
                        ->string(),

                    TextInput::make('model_id')
                        ->label(__('crud.auditLogs.inputs.model_id.label'))
                        ->required()
                        ->numeric()
                        ->step(1),

                    RichEditor::make('changes')
                        ->label(__('crud.auditLogs.inputs.changes.label'))
                        ->required()
                        ->fileAttachmentsVisibility('public'),

                    TextInput::make('ip_address')
                        ->label(__('crud.auditLogs.inputs.ip_address.label'))
                        ->nullable()
                        ->string(),

                    TextInput::make('user_agent')
                        ->label(__('crud.auditLogs.inputs.user_agent.label'))
                        ->nullable()
                        ->string(),

                    DateTimePicker::make('deleted_at')
                        ->label(__('crud.auditLogs.inputs.deleted_at.label'))
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
                TextColumn::make('action')->label(
                    __('crud.auditLogs.inputs.action.label')
                ),

                TextColumn::make('user.name')->label('User'),

                TextColumn::make('model_type')->label(
                    __('crud.auditLogs.inputs.model_type.label')
                ),

                TextColumn::make('model_id')->label(
                    __('crud.auditLogs.inputs.model_id.label')
                ),

                TextColumn::make('changes')
                    ->label(__('crud.auditLogs.inputs.changes.label'))
                    ->limit(255),

                TextColumn::make('ip_address')->label(
                    __('crud.auditLogs.inputs.ip_address.label')
                ),

                TextColumn::make('user_agent')->label(
                    __('crud.auditLogs.inputs.user_agent.label')
                ),

                TextColumn::make('deleted_at')
                    ->label(__('crud.auditLogs.inputs.deleted_at.label'))
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
            'index' => Pages\ListAuditLogs::route('/'),
            'create' => Pages\CreateAuditLog::route('/create'),
            'view' => Pages\ViewAuditLog::route('/{record}'),
            'edit' => Pages\EditAuditLog::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
    }
}
