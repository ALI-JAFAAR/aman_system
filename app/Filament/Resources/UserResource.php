<?php


namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers\AdminRecordsRelationManager;
use App\Filament\Resources\UserResource\RelationManagers\AffiliationsRelationManager;
use App\Filament\Resources\UserResource\RelationManagers\EmployeeRelationManager;
use App\Filament\Resources\UserResource\RelationManagers\ProfessionsRelationManager;
use App\Models\User;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Form;
use Filament\Forms;
use Filament\Tables\Filters\Filter;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationGroup = 'الإدارة';
    protected static ?string $navigationLabel = 'المستخدمون';
    protected static ?int    $navigationSort  = 80;
    protected static ?string $navigationIcon  = 'heroicon-o-user-group';


    public static function form(Form $form): Form
    {
        return $form->schema([ /* لن نستخدم إنشاء/تعديل هنا */]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('#')->sortable(),
                Tables\Columns\TextColumn::make('name')->label('الاسم')->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('email')->label('البريد')->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('profile.phone')
                    ->label('الهاتف')
                    ->getStateUsing(fn(User $r) => $r->profile?->phone)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')->label('تاريخ الإنشاء')->dateTime()->since()->sortable(),
            ])
            ->filters([
                Filter::make('has_balance')
                    ->label('لديه رصيد مستحق')
                    ->query(fn($q) => $q->whereHas('invoices', fn($qq) => $qq->where('balance', '>', 0))
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('عرض التفاصيل'),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getRelations(): array{
        return [
            EmployeeRelationManager::class,
            AdminRecordsRelationManager::class,
            ProfessionsRelationManager::class,
            AffiliationsRelationManager::class,
        ];
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'view' => Pages\ViewUser::route('/{record}'),
        ];
    }
}

//
//namespace App\Filament\Resources;
//
//use App\Filament\Resources\UserResource\Pages;
//use App\Filament\Resources\UserResource\RelationManagers\AdminRecordsRelationManager;
//use App\Filament\Resources\UserResource\RelationManagers\AffiliationsRelationManager;
//use App\Filament\Resources\UserResource\RelationManagers\ProfessionsRelationManager;
//use App\Filament\Resources\UserResource\RelationManagers\ProfileRelationManager;
//use App\Filament\Resources\UserResource\RelationManagers\UserOfferingsRelationManager;
//use App\Filament\Resources\UserResource\RelationManagers\UserServicesRelationManager;
//use App\Filament\Resources\UserResource\RelationManagers\WalletRelationManager;
//use App\Models\Organization;
//use App\Models\User;
//use Filament\Forms;
//use Filament\Forms\Components\DatePicker;
//use Filament\Forms\Components\FileUpload;
//use Filament\Forms\Components\KeyValue;
//use Filament\Forms\Components\Section;
//use Filament\Forms\Components\TextInput;
//use Filament\Forms\Components\Repeater;
//use Filament\Forms\Components\Select;
//use Filament\Forms\Form;
//use Filament\Resources\Resource;
//use Filament\Tables;
//use Filament\Tables\Columns\TextColumn;
//use Filament\Tables\Table;
//use Illuminate\Support\Facades\Hash;
//
//class UserResource extends Resource
//{
//    protected static ?string $model = User::class;
//    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
//
//    public static function form(Form $form): Form
//    {
//        return $form->schema([
//            // ======= بيانات الحساب =======
//            Section::make()
//                ->schema([
//                    TextInput::make('name')->label('الاسم')->required(),
//                    TextInput::make('email')->label('البريد الإلكتروني')->email()->required()->unique(ignoreRecord: true),
//                    TextInput::make('password')
//                        ->label('كلمة المرور')->password()
//                        ->required(fn($livewire) => $livewire instanceof Pages\CreateUser)
//                        ->minLength(8)
//                        ->dehydrated(fn($state) => filled($state))
//                        ->dehydrateStateUsing(fn($state) => Hash::make($state)),
//                ])
//                ->columns(2),
//
//            // ======= الملف الشخصي (hasOne) =======
//            Section::make('الملف الشخصي')
//                ->description('بيانات التعريف والاتصال')
//                ->schema([
//                    TextInput::make('name')->label('الاسم الرباعي')->required(),
//                    TextInput::make('mother_name')->label('اسم الأم'),
//                    TextInput::make('national_id')->label('رقم الهوية'),
//                    DatePicker::make('date_of_birth')->label('تاريخ الميلاد')->nullable(),
//                    TextInput::make('place_of_birth')->label('مكان الميلاد')->nullable(),
//                    TextInput::make('phone')->label('رقم الهاتف')->required(),
//                    TextInput::make('address_province')->label('المحافظة')->required(),
//                    TextInput::make('address_district')->label('القضاء/المنطقة')->nullable(),
//                    TextInput::make('address_subdistrict')->label('الناحية/الحي')->nullable(),
//                    TextInput::make('address_details')->label('تفاصيل العنوان')->nullable(),
//                    KeyValue::make('extra_data')->label('بيانات إضافية')->keyLabel('المفتاح')->valueLabel('القيمة')->nullable(),
//                    FileUpload::make('image')->label('الصورة الشخصية')
//                        ->image()->directory('user_profiles/images')->visibility('public')->nullable(),
//                ])
//                ->relationship('userProfiles') // يحفظ UserProfile تلقائياً
//                ->columns(2),
//
//            // ======= بيانات الموظف (hasMany لكن عنصر واحد فقط) =======
//            Section::make('بيانات الموظف')
//                ->description('هذا القسم مطلوب لحسابات الموظفين كي يُستخدموا مُصدّرين للفواتير والقيود.')
//                ->schema([
//                    Repeater::make('employees')
//                        ->relationship('employees')   // يحفظ في جدول employees
//                        ->schema([
//                            TextInput::make('job_title')->label('المسمى الوظيفي')->required(),
//                            TextInput::make('salary')->label('الراتب')->numeric()->minValue(0)->step(0.01)->default(0),
//                            Select::make('organization_id')
//                                ->label('الجهة (اختياري لموظفي المنصة)')
//                                ->options(Organization::query()->orderBy('name')->pluck('name','id')->toArray())
//                                ->searchable()
//                                ->preload()
//                                ->placeholder('موظف منصة (بدون جهة)'),
//                        ])
//                        ->defaultItems(1)   // عنصر واحد افتراضياً عند الإنشاء
//                        ->minItems(1)       // اجعلها 1 إن كنت تريد إلزام كل مستخدم يكون موظفاً
//                        ->maxItems(1)
//                        ->columns(2)
//                        ->cloneable(false)
//                        ->reorderable(false),
//                ]),
//        ]);
//    }
//
//    public static function table(Table $table): Table{
//
//        return $table->columns([
//            TextColumn::make('id')->label('#'),
//            TextColumn::make('email')->label('البريد الإلكتروني')->sortable()->searchable(),
//            TextColumn::make('userProfiles.name')->label('الاسم')->sortable()->searchable(),
//            TextColumn::make('employees.0.job_title')->label('الوظيفة')->toggleable(),   // أول سجل موظف
//            TextColumn::make('employees.0.organization.name')->label('الجهة')->toggleable(),
//            TextColumn::make('created_at')->label('تاريخ الإنشاء')->dateTime('Y-m-d'),
//        ])->actions([
//            Tables\Actions\EditAction::make(),
//        ])->bulkActions([
//            Tables\Actions\BulkActionGroup::make([
//                Tables\Actions\DeleteBulkAction::make(),
//            ]),
//        ]);
//    }
//
//    public static function getRelations(): array{
//        return [
//            ProfileRelationManager::class,
//            AffiliationsRelationManager::class,
//            UserOfferingsRelationManager::class,
//            UserServicesRelationManager::class,
//            ProfessionsRelationManager::class,
//            AdminRecordsRelationManager::class,
//            WalletRelationManager::class,
//        ];
//    }
//
//    public static function getPages(): array
//    {
//        return [
//            'index'  => Pages\ListUsers::route('/'),
//            'create' => Pages\CreateUser::route('/create'),
//            'edit'   => Pages\EditUser::route('/{record}/edit'),
//        ];
//    }
//}
