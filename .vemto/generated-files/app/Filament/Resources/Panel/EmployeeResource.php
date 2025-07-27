<?php

namespace App\Filament\Resources\Panel;

use Filament\Forms;
use Filament\Tables;
use Livewire\Component;
use Filament\Forms\Form;
use App\Models\Employee;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DateTimePicker;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Panel\EmployeeResource\Pages;
use App\Filament\Resources\Panel\EmployeeResource\RelationManagers;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Admin';

    public static function getModelLabel(): string
    {
        return __('crud.employees.itemTitle');
    }

    public static function getPluralModelLabel(): string
    {
        return __('crud.employees.collectionTitle');
    }

    public static function getNavigationLabel(): string
    {
        return __('crud.employees.collectionTitle');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->schema([
                Grid::make(['default' => 1])->schema([
                    TextInput::make('job_title')
                        ->label(__('crud.employees.inputs.job_title.label'))
                        ->required()
                        ->string()
                        ->autofocus(),

                    TextInput::make('salary')
                        ->label(__('crud.employees.inputs.salary.label'))
                        ->required()
                        ->numeric()
                        ->step(1),

                    Select::make('user_id')
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

                    DateTimePicker::make('deleted_at')
                        ->label(__('crud.employees.inputs.deleted_at.label'))
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
                TextColumn::make('job_title')->label(
                    __('crud.employees.inputs.job_title.label')
                ),

                TextColumn::make('salary')->label(
                    __('crud.employees.inputs.salary.label')
                ),

                TextColumn::make('user.name')->label('User'),

                TextColumn::make('organization.name')->label('Organization'),

                TextColumn::make('deleted_at')
                    ->label(__('crud.employees.inputs.deleted_at.label'))
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
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'view' => Pages\ViewEmployee::route('/{record}'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
    }
}
