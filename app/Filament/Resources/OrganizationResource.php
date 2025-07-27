<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrganizationResource\Pages;
use App\Filament\Resources\OrganizationResource\RelationManagers\AffiliationsRelationManager;
use App\Filament\Resources\OrganizationResource\RelationManagers\ContractsRelationManager;
use App\Filament\Resources\OrganizationResource\RelationManagers\EmployeesRelationManager;
use App\Filament\Resources\OrganizationResource\RelationManagers\OfferingDistributionsRelationManager;
use App\Filament\Resources\OrganizationResource\RelationManagers\PartnerOfferingsRelationManager;
use App\Models\Organization;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;


class OrganizationResource extends Resource
{
    protected static ?string $model = Organization::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationGroup = 'إدارة المنظومة';
    protected static ?int $navigationSort = 1;
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form{
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        TextInput::make('name')
                            ->label('اسم الجهة')
                            ->required()
                            ->maxLength(150),
                        Select::make('type')
                            ->label('نوع الجهة')
                            ->options([
                                'general_union'           => 'اتحاد عام',
                                'sub_union'               => 'اتحاد فرعي',
                                'trade_union'             => 'نقابة',
                                'government_institution'  => 'مؤسسة حكومية / منفذ',
                                'insurance_company'       => 'شركة تأمين',
                                'law_firm'                => 'مكتب محاماة',
                                'platform'                => 'منصة أمان',
                            ])
                            ->required(),
                        TextInput::make('code')
                            ->label('رمز الجهة')
                            ->helperText('رمز فريد اختياري لربط العقود والتوزيعات')
                            ->maxLength(50),
                        Textarea::make('notes')
                            ->label('ملاحظات')
                            ->rows(3),

                        FileUpload::make('logo')
                            ->label('شعار الجهة')
                            ->image()                     // قيد لتحميل صورة فقط
                            ->directory('organizations/logos')
                            ->visibility('public')
                            ->maxSize(1024)               // 1MB
                            ->helperText('يرجى رفع شعار بصيغة PNG أو JPG'),

                        FileUpload::make('documents')
                            ->label('مستندات وعقود')
                            ->directory('organizations/documents')
                            ->multiple()                  // السماح بأكثر من ملف
                            ->visibility('public')
                            ->maxSize(2048)               // 2MB لكل ملف
                            ->helperText('يمكن رفع عدة مستندات PDF أو صور'),
                    ])
                    ->columns(2),

                Section::make('العقود المرتبطة')
                    ->schema([
                        Repeater::make('contracts')
                            ->relationship()
                            ->schema([
                                Select::make('service_type')
                                    ->label('نوع الخدمة')
                                    ->options([
                                        'identity_issue' => 'إصدار هوية',
                                        'route_card'     => 'بطاقة خط السير',
                                        'claim'          => 'مطالبة',
                                        'other'          => 'أخرى',
                                    ])
                                    ->required(),
                                Select::make('initiator_type')
                                    ->label('منشئ المعاملة')
                                    ->options([
                                        'platform' => 'منصة أمان',
                                        'partner'  => 'شريك',
                                    ])
                                    ->required(),
                                TextInput::make('platform_rate')
                                    ->label('نسبة المنصة %')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(100),
                                TextInput::make('organization_rate')
                                    ->label('نسبة الجهة %')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(100),
                                TextInput::make('partner_rate')
                                    ->label('نسبة الشريك %')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(100),
                                DatePicker::make('contract_start')
                                    ->label('تاريخ البداية')
                                    ->required(),
                                DatePicker::make('contract_end')
                                    ->label('تاريخ الانتهاء')
                                    ->nullable(),
                            ])
                            ->columns(3),
                    ])
                    ->collapsed(),
            ]);


    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('معرّف'),
                TextColumn::make('name')->label('اسم الجهة')->sortable()->searchable(),
                ImageColumn::make('logo_url')
                    ->label('الشعار')
                    ->circular(),
                TextColumn::make('type')
                    ->label('النوع')
                    ->badge()
//                    ->enum([
//                        'general_union'          => 'اتحاد عام',
//                        'sub_union'              => 'اتحاد فرعي',
//                        'trade_union'            => 'نقابة',
//                        'government_institution' => 'مؤسسة حكومية',
//                        'insurance_company'      => 'شركة تأمين',
//                        'law_firm'               => 'مكتب محاماة',
//                        'platform'               => 'منصة أمان',
//                    ])
                    ->colors([
                        'primary'   => 'platform',
                        'success'   => 'insurance_company',
                        'warning'   => 'trade_union',
                        'danger'    => 'law_firm',
                        'secondary' => fn($state) => in_array($state, ['general_union','sub_union','government_institution']),
                    ]),
                TextColumn::make('contracts_count')
                    ->label('عدد العقود')
                    ->counts('contracts'),
                TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime('Y-m-d'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('فلتر بنوع الجهة')
                    ->options([
                        'general_union'          => 'اتحاد عام',
                        'sub_union'              => 'اتحاد فرعي',
                        'trade_union'            => 'نقابة',
                        'government_institution' => 'مؤسسة حكومية',
                        'insurance_company'      => 'شركة تأمين',
                        'law_firm'               => 'مكتب محاماة',
                        'platform'               => 'منصة أمان',
                    ]),
            ])

            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array{
        return [
            EmployeesRelationManager::class,
            AffiliationsRelationManager::class,
            PartnerOfferingsRelationManager::class,
            ContractsRelationManager::class,                  // إدارة العقود
            OfferingDistributionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'   => Pages\ListOrganizations::route('/'),
            'create'  => Pages\CreateOrganization::route('/create'),
            'edit'    => Pages\EditOrganization::route('/{record}/edit'),
        ];
    }
}
