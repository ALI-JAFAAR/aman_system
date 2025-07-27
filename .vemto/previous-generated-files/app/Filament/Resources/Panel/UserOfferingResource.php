<?php

namespace App\Filament\Resources\Panel;

use Filament\Forms;
use Filament\Tables;
use Livewire\Component;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\UserOffering;
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
use App\Filament\Resources\Panel\UserOfferingResource\Pages;
use App\Filament\Resources\Panel\UserOfferingResource\RelationManagers;

class UserOfferingResource extends Resource
{
    protected static ?string $model = UserOffering::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Admin';

    public static function getModelLabel(): string
    {
        return __('crud.userOfferings.itemTitle');
    }

    public static function getPluralModelLabel(): string
    {
        return __('crud.userOfferings.collectionTitle');
    }

    public static function getNavigationLabel(): string
    {
        return __('crud.userOfferings.collectionTitle');
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

                    Select::make('status')
                        ->label(__('crud.userOfferings.inputs.status.label'))
                        ->required()
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->options([
                            'pending' => 'Pending',
                            'active' => 'Active',
                            'rejected' => 'Rejected',
                        ]),

                    TextInput::make('platform_generated_number')
                        ->label(
                            __(
                                'crud.userOfferings.inputs.platform_generated_number.label'
                            )
                        )
                        ->nullable()
                        ->string(),

                    TextInput::make('partner_filled_number')
                        ->label(
                            __(
                                'crud.userOfferings.inputs.partner_filled_number.label'
                            )
                        )
                        ->nullable()
                        ->string(),

                    TextInput::make('applied_at')
                        ->label(
                            __('crud.userOfferings.inputs.applied_at.label')
                        )
                        ->required()
                        ->string(),

                    TextInput::make('activated_at')
                        ->label(
                            __('crud.userOfferings.inputs.activated_at.label')
                        )
                        ->required()
                        ->string(),

                    TextInput::make('rejected_at')
                        ->label(
                            __('crud.userOfferings.inputs.rejected_at.label')
                        )
                        ->required()
                        ->string(),

                    RichEditor::make('notes')
                        ->label(__('crud.userOfferings.inputs.notes.label'))
                        ->required()
                        ->string()
                        ->fileAttachmentsVisibility('public'),

                    Select::make('partner_offering_id')
                        ->label('Partner Offering')
                        ->required()
                        ->relationship('partnerOffering', 'contract_start')
                        ->searchable()
                        ->preload()
                        ->native(false),

                    DateTimePicker::make('deleted_at')
                        ->label(
                            __('crud.userOfferings.inputs.deleted_at.label')
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

                TextColumn::make('status')->label(
                    __('crud.userOfferings.inputs.status.label')
                ),

                TextColumn::make('platform_generated_number')->label(
                    __(
                        'crud.userOfferings.inputs.platform_generated_number.label'
                    )
                ),

                TextColumn::make('partner_filled_number')->label(
                    __('crud.userOfferings.inputs.partner_filled_number.label')
                ),

                TextColumn::make('applied_at')->label(
                    __('crud.userOfferings.inputs.applied_at.label')
                ),

                TextColumn::make('activated_at')->label(
                    __('crud.userOfferings.inputs.activated_at.label')
                ),

                TextColumn::make('rejected_at')->label(
                    __('crud.userOfferings.inputs.rejected_at.label')
                ),

                TextColumn::make('notes')
                    ->label(__('crud.userOfferings.inputs.notes.label'))
                    ->limit(255),

                TextColumn::make('partnerOffering.contract_start')->label(
                    'Partner Offering'
                ),

                TextColumn::make('deleted_at')
                    ->label(__('crud.userOfferings.inputs.deleted_at.label'))
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
            'index' => Pages\ListUserOfferings::route('/'),
            'create' => Pages\CreateUserOffering::route('/create'),
            'view' => Pages\ViewUserOffering::route('/{record}'),
            'edit' => Pages\EditUserOffering::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
    }
}
