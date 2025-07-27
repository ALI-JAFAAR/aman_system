<?php

namespace App\Filament\Resources\Panel;

use Filament\Forms;
use Filament\Tables;
use Livewire\Component;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\UserAffiliation;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DateTimePicker;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Panel\UserAffiliationResource\Pages;
use App\Filament\Resources\Panel\UserAffiliationResource\RelationManagers;

class UserAffiliationResource extends Resource
{
    protected static ?string $model = UserAffiliation::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Admin';

    public static function getModelLabel(): string
    {
        return __('crud.userAffiliations.itemTitle');
    }

    public static function getPluralModelLabel(): string
    {
        return __('crud.userAffiliations.collectionTitle');
    }

    public static function getNavigationLabel(): string
    {
        return __('crud.userAffiliations.collectionTitle');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->schema([
                Grid::make(['default' => 1])->schema([
                    Select::make('status')
                        ->label(__('crud.userAffiliations.inputs.status.label'))
                        ->required()
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->options([
                            'pending' => 'Pending',
                            'approved' => 'Approved',
                            'rejected' => 'Rejected',
                        ]),

                    Select::make('organization_id')
                        ->label('Organization')
                        ->required()
                        ->relationship('organization', 'name')
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

                    DateTimePicker::make('joined_at')
                        ->label(
                            __('crud.userAffiliations.inputs.joined_at.label')
                        )
                        ->rules(['date'])
                        ->required()
                        ->native(false),

                    DateTimePicker::make('deleted_at')
                        ->label(
                            __('crud.userAffiliations.inputs.deleted_at.label')
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
                TextColumn::make('status')->label(
                    __('crud.userAffiliations.inputs.status.label')
                ),

                TextColumn::make('organization.name')->label('Organization'),

                TextColumn::make('user.name')->label('User'),

                TextColumn::make('joined_at')
                    ->label(__('crud.userAffiliations.inputs.joined_at.label'))
                    ->since(),

                TextColumn::make('deleted_at')
                    ->label(__('crud.userAffiliations.inputs.deleted_at.label'))
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
            'index' => Pages\ListUserAffiliations::route('/'),
            'create' => Pages\CreateUserAffiliation::route('/create'),
            'view' => Pages\ViewUserAffiliation::route('/{record}'),
            'edit' => Pages\EditUserAffiliation::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
    }
}
