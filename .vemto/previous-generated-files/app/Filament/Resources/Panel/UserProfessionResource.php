<?php

namespace App\Filament\Resources\Panel;

use Filament\Forms;
use Filament\Tables;
use Livewire\Component;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\UserProfession;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\DateTimePicker;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Panel\UserProfessionResource\Pages;
use App\Filament\Resources\Panel\UserProfessionResource\RelationManagers;

class UserProfessionResource extends Resource
{
    protected static ?string $model = UserProfession::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Admin';

    public static function getModelLabel(): string
    {
        return __('crud.userProfessions.itemTitle');
    }

    public static function getPluralModelLabel(): string
    {
        return __('crud.userProfessions.collectionTitle');
    }

    public static function getNavigationLabel(): string
    {
        return __('crud.userProfessions.collectionTitle');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->schema([
                Grid::make(['default' => 1])->schema([
                    Select::make('user_affiliation_id')
                        ->label('User Affiliation')
                        ->required()
                        ->relationship('userAffiliation', 'joined_at')
                        ->searchable()
                        ->preload()
                        ->native(false),

                    Select::make('profession_id')
                        ->label('Profession')
                        ->required()
                        ->relationship('profession', 'name')
                        ->searchable()
                        ->preload()
                        ->native(false),

                    Select::make('specialization_id')
                        ->label('Specialization')
                        ->required()
                        ->relationship('specialization', 'name')
                        ->searchable()
                        ->preload()
                        ->native(false),

                    Select::make('status')
                        ->label(__('crud.userProfessions.inputs.status.label'))
                        ->required()
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->options([
                            'pending' => 'Pending',
                            'approved' => 'Approved',
                            'rejected' => 'Rejected',
                        ]),

                    DatePicker::make('applied_at')
                        ->label(
                            __('crud.userProfessions.inputs.applied_at.label')
                        )
                        ->rules(['date'])
                        ->nullable()
                        ->native(false),

                    DatePicker::make('approved_at')
                        ->label(
                            __('crud.userProfessions.inputs.approved_at.label')
                        )
                        ->rules(['date'])
                        ->nullable()
                        ->native(false),

                    Select::make('approved_by')
                        ->label('Employee')
                        ->required()
                        ->relationship('employee', 'job_title')
                        ->searchable()
                        ->preload()
                        ->native(false),

                    RichEditor::make('notes')
                        ->label(__('crud.userProfessions.inputs.notes.label'))
                        ->required()
                        ->string()
                        ->fileAttachmentsVisibility('public'),

                    DateTimePicker::make('deleted_at')
                        ->label(
                            __('crud.userProfessions.inputs.deleted_at.label')
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
                TextColumn::make('userAffiliation.joined_at')->label(
                    'User Affiliation'
                ),

                TextColumn::make('profession.name')->label('Profession'),

                TextColumn::make('specialization.name')->label(
                    'Specialization'
                ),

                TextColumn::make('status')->label(
                    __('crud.userProfessions.inputs.status.label')
                ),

                TextColumn::make('applied_at')
                    ->label(__('crud.userProfessions.inputs.applied_at.label'))
                    ->since(),

                TextColumn::make('approved_at')
                    ->label(__('crud.userProfessions.inputs.approved_at.label'))
                    ->since(),

                TextColumn::make('employee.job_title')->label('Employee'),

                TextColumn::make('notes')
                    ->label(__('crud.userProfessions.inputs.notes.label'))
                    ->limit(255),

                TextColumn::make('deleted_at')
                    ->label(__('crud.userProfessions.inputs.deleted_at.label'))
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
            'index' => Pages\ListUserProfessions::route('/'),
            'create' => Pages\CreateUserProfession::route('/create'),
            'view' => Pages\ViewUserProfession::route('/{record}'),
            'edit' => Pages\EditUserProfession::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
    }
}
