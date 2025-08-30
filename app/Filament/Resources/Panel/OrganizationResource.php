<?php

namespace App\Filament\Resources\Panel;

use Filament\Forms;
use Filament\Tables;
use Livewire\Component;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Organization;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\RichEditor;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Panel\OrganizationResource\Pages;
use App\Filament\Resources\Panel\OrganizationResource\RelationManagers;

class OrganizationResource extends Resource{
    protected static ?string $model = Organization::class;

    protected static ?string $navigationGroup = 'الإدارة';
    protected static ?string $navigationLabel = 'الجهات';
    protected static ?int    $navigationSort  = 10;
    protected static ?string $navigationIcon  = 'heroicon-o-building-office-2';


    public static function getModelLabel(): string{
        return __('crud.organizations.itemTitle');
    }

    public static function getPluralModelLabel(): string{
        return __('crud.organizations.collectionTitle');
    }

    public static function getNavigationLabel(): string{
        return __('crud.organizations.collectionTitle');
    }

    public static function form(Form $form): Form{

        return $form->schema([
            Section::make()->schema([
                Grid::make(['default' => 1])->schema([
                    TextInput::make('name')
                        ->label(__('crud.organizations.inputs.name.label'))
                        ->required()
                        ->string()
                        ->autofocus(),

                    Select::make('type')
                        ->label(__('crud.organizations.inputs.type.label'))
                        ->required()
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->options([
                            'general_union'           => 'اتحاد عام',
                            'sub_union'               => 'اتحاد فرعي',
                            'trade_union'             => 'نقابة',
                            'government_institution'  => 'مؤسسة حكومية / منفذ',
                            'insurance_company'       => 'شركة تأمين',
                            'law_firm'                => 'مكتب محاماة',
                            'platform'                => 'منصة أمان',
                            'organization'            => 'مؤسسات',
                            'guild'                   => 'نقابة',
                        ]),

                    RichEditor::make('details')
                        ->label(__('crud.organizations.inputs.details.label'))
                        ->required()
                        ->string()
                        ->fileAttachmentsVisibility('public'),

                    Select::make('organization_id')
                        ->label('Organization')
                        ->nullable(
                            fn(string $context): bool => $context === 'create'
                        )
                        ->dehydrated(fn($state) => filled($state))
                        ->relationship('organization', 'name')
                        ->searchable()
                        ->preload()
                        ->native(false),

                    TextInput::make('code')
                        ->label(__('crud.organizations.inputs.code.label'))
                        ->required()
                        ->string()
                        ->unique('organizations', 'code', ignoreRecord: true),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table{
        return $table
            ->poll('60s')
            ->columns([
                TextColumn::make('name')->label(
                    __('crud.organizations.inputs.name.label')
                ),

                TextColumn::make('type')->label(
                    __('crud.organizations.inputs.type.label')
                ),

                TextColumn::make('details')
                    ->label(__('crud.organizations.inputs.details.label'))
                    ->limit(255),

                TextColumn::make('organization.name')->label('Organization'),

                TextColumn::make('code')->label(
                    __('crud.organizations.inputs.code.label')
                ),
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

    public static function getRelations(): array{
        return [];
    }

    public static function getPages(): array{
        return [
            'index' => Pages\ListOrganizations::route('/'),
            'create' => Pages\CreateOrganization::route('/create'),
            'view' => Pages\ViewOrganization::route('/{record}'),
            'edit' => Pages\EditOrganization::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder{
        return parent::getEloquentQuery()->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
    }
}
