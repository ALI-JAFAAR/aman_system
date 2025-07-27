<?php

namespace App\Filament\Resources\Panel;

use Filament\Forms;
use Filament\Tables;
use Livewire\Component;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\UserProfile;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Panel\UserProfileResource\Pages;
use App\Filament\Resources\Panel\UserProfileResource\RelationManagers;

class UserProfileResource extends Resource
{
    protected static ?string $model = UserProfile::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Admin';

    public static function getModelLabel(): string
    {
        return __('crud.userProfiles.itemTitle');
    }

    public static function getPluralModelLabel(): string
    {
        return __('crud.userProfiles.collectionTitle');
    }

    public static function getNavigationLabel(): string
    {
        return __('crud.userProfiles.collectionTitle');
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

                    TextInput::make('name')
                        ->label(__('crud.userProfiles.inputs.name.label'))
                        ->required()
                        ->string(),

                    TextInput::make('mother_name')
                        ->label(
                            __('crud.userProfiles.inputs.mother_name.label')
                        )
                        ->required()
                        ->string(),

                    TextInput::make('national_id')
                        ->label(
                            __('crud.userProfiles.inputs.national_id.label')
                        )
                        ->required()
                        ->string(),

                    TextInput::make('date_of_birth')
                        ->label(
                            __('crud.userProfiles.inputs.date_of_birth.label')
                        )
                        ->required()
                        ->string(),

                    TextInput::make('place_of_birth')
                        ->label(
                            __('crud.userProfiles.inputs.place_of_birth.label')
                        )
                        ->required()
                        ->string(),

                    TextInput::make('phone')
                        ->label(__('crud.userProfiles.inputs.phone.label'))
                        ->required()
                        ->string(),

                    TextInput::make('address_province')
                        ->label(
                            __(
                                'crud.userProfiles.inputs.address_province.label'
                            )
                        )
                        ->required()
                        ->string(),

                    TextInput::make('address_district')
                        ->label(
                            __(
                                'crud.userProfiles.inputs.address_district.label'
                            )
                        )
                        ->required()
                        ->string(),

                    TextInput::make('address_subdistrict')
                        ->label(
                            __(
                                'crud.userProfiles.inputs.address_subdistrict.label'
                            )
                        )
                        ->required()
                        ->string(),

                    TextInput::make('address_details')
                        ->label(
                            __('crud.userProfiles.inputs.address_details.label')
                        )
                        ->required()
                        ->string(),

                    RichEditor::make('extra_data')
                        ->label(__('crud.userProfiles.inputs.extra_data.label'))
                        ->required()
                        ->fileAttachmentsVisibility('public'),

                    FileUpload::make('image')
                        ->label(__('crud.userProfiles.inputs.image.label'))
                        ->rules(['image'])
                        ->required(
                            fn(string $context): bool => $context === 'create'
                        )
                        ->dehydrated(fn($state) => filled($state))
                        ->maxSize(1024)
                        ->image()
                        ->imageEditor()
                        ->imageEditorAspectRatios([null, '16:9', '4:3', '1:1']),
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

                TextColumn::make('name')->label(
                    __('crud.userProfiles.inputs.name.label')
                ),

                TextColumn::make('mother_name')->label(
                    __('crud.userProfiles.inputs.mother_name.label')
                ),

                TextColumn::make('national_id')->label(
                    __('crud.userProfiles.inputs.national_id.label')
                ),

                TextColumn::make('date_of_birth')->label(
                    __('crud.userProfiles.inputs.date_of_birth.label')
                ),

                TextColumn::make('place_of_birth')->label(
                    __('crud.userProfiles.inputs.place_of_birth.label')
                ),

                TextColumn::make('phone')->label(
                    __('crud.userProfiles.inputs.phone.label')
                ),

                TextColumn::make('address_province')->label(
                    __('crud.userProfiles.inputs.address_province.label')
                ),

                TextColumn::make('address_district')->label(
                    __('crud.userProfiles.inputs.address_district.label')
                ),

                TextColumn::make('address_subdistrict')->label(
                    __('crud.userProfiles.inputs.address_subdistrict.label')
                ),

                TextColumn::make('address_details')->label(
                    __('crud.userProfiles.inputs.address_details.label')
                ),

                TextColumn::make('extra_data')
                    ->label(__('crud.userProfiles.inputs.extra_data.label'))
                    ->limit(255),

                ImageColumn::make('image')
                    ->label(__('crud.userProfiles.inputs.image.label'))
                    ->visibility('public'),
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
            'index' => Pages\ListUserProfiles::route('/'),
            'create' => Pages\CreateUserProfile::route('/create'),
            'view' => Pages\ViewUserProfile::route('/{record}'),
            'edit' => Pages\EditUserProfile::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
    }
}
