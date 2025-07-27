<?php

namespace App\Filament\Resources\Panel;

use Filament\Forms;
use Filament\Tables;
use Livewire\Component;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Notification;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\DateTimePicker;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Panel\NotificationResource\Pages;
use App\Filament\Resources\Panel\NotificationResource\RelationManagers;

class NotificationResource extends Resource
{
    protected static ?string $model = Notification::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Admin';

    public static function getModelLabel(): string
    {
        return __('crud.notifications.itemTitle');
    }

    public static function getPluralModelLabel(): string
    {
        return __('crud.notifications.collectionTitle');
    }

    public static function getNavigationLabel(): string
    {
        return __('crud.notifications.collectionTitle');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->schema([
                Grid::make(['default' => 1])->schema([
                    TextInput::make('type')
                        ->label(__('crud.notifications.inputs.type.label'))
                        ->required()
                        ->string()
                        ->autofocus(),

                    TextInput::make('notifiable_type')
                        ->label(
                            __(
                                'crud.notifications.inputs.notifiable_type.label'
                            )
                        )
                        ->required()
                        ->string(),

                    TextInput::make('notifiable_id')
                        ->label(
                            __('crud.notifications.inputs.notifiable_id.label')
                        )
                        ->required()
                        ->string(),

                    RichEditor::make('data')
                        ->label(__('crud.notifications.inputs.data.label'))
                        ->required()
                        ->fileAttachmentsVisibility('public'),

                    DateTimePicker::make('read_at')
                        ->label(__('crud.notifications.inputs.read_at.label'))
                        ->rules(['date'])
                        ->required()
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
                TextColumn::make('type')->label(
                    __('crud.notifications.inputs.type.label')
                ),

                TextColumn::make('notifiable_type')->label(
                    __('crud.notifications.inputs.notifiable_type.label')
                ),

                TextColumn::make('notifiable_id')->label(
                    __('crud.notifications.inputs.notifiable_id.label')
                ),

                TextColumn::make('data')
                    ->label(__('crud.notifications.inputs.data.label'))
                    ->limit(255),

                TextColumn::make('read_at')
                    ->label(__('crud.notifications.inputs.read_at.label'))
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
            'index' => Pages\ListNotifications::route('/'),
            'create' => Pages\CreateNotification::route('/create'),
            'view' => Pages\ViewNotification::route('/{record}'),
            'edit' => Pages\EditNotification::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
    }
}
