<?php

namespace App\Filament\Pages;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\Organization;
use App\Models\UserAffiliation;
use App\Models\Profession;
use App\Models\Specialization;
use App\Models\UserProfession;
use App\Models\PartnerOffering;
use App\Models\UserOffering;
use App\Models\Service;
use App\Models\UserService;
use App\Models\OfferingDistribution;
use App\Models\Wallet;
use App\Models\LedgerEntry;
use App\Models\Employee;

use App\Services\AffiliationPostingService;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;

use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Get;
use Filament\Forms\Set;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use App\Enums\OrganizationType;
use App\Models\Package;
use Filament\Forms\Components\Radio;
class AffiliationWizard extends Page implements HasForms{
    use InteractsWithForms;
    public ?array $data = [];
    protected static ?string $navigationGroup = 'الانتساب';
    protected static ?string $navigationLabel = 'تسجيل انتساب جديد';
    protected static ?int    $navigationSort  = 10;
    protected static ?string $navigationIcon  = 'heroicon-o-rectangle-stack';

    protected static ?string $slug            = 'affiliation';
    protected static string  $view            = 'filament.pages.affiliation-wizard';

    // بيانات الحساب والملف الشخصي
    public string  $email          = '';
    public string  $password       = '';
    public string  $name           = '';
    public ?string $mother_name    = null;
    public ?string $national_id    = null;
    public ?string $date_of_birth  = null;
    public ?string $place_of_birth = null;
    public string  $phone          = '';

    public string  $address_province    = '';
    public string  $address_district    = '';
    public string  $address_subdistrict = '';
    public string  $address_details     = '';
    public array   $extra_data          = [];
    public string  $image               = '';

    public string $employment_sector = 'private';
    // موظف مُصدِّر العملية
    public ?int $issuer_employee_id = null;

    // المهنة/الاختصاص
    public ?int $profession_id = null;
    public ?int $specialization_id = null;
    public ?string $notes = null;
    public array $offerings = [];
    public array $service_requests = [];

    public bool  $has_family       = false;
    public array $family_members   = [];
    public array $affiliations = [];

    public bool  $has_related_workers        = false;
    public array $related_workers_existing   = []; // array of user IDs
    public array $related_workers_new        = []; // array of rows: name,email,password

    // Payment step state (because you are not using a data state path)
    public bool $take_payment_now = false;
    public ?string $payment_method = null;
// (optional) if you later allow partial payment:
    public ?float $amount_taken_now = null;
    public string $discount_type = 'none';
    public ?float $affiliation_fee = 0;// none | percent | fixed
    public ?float $discount_value = 0;               // القيمة: نسبة % أو مبلغ ثابت
    public string $discount_funded_by = 'platform';
    // مجموع ملخص (عرض فقط)
    public float $grand_total = 0;

    public function mount(): void{
        $this->issuer_employee_id = Employee::where('user_id', auth()->id())->value('id');
        if (empty($this->affiliations)) {
            $this->affiliations = [[]];
        }

        if (empty($this->offerings)) {
            $this->offerings = [[
                'partner_id'          => null,
                'package_id'          => null,
                'partner_offering_id' => null,
                'price'               => null,
                'details'             => null,
            ]];
        }
    }

    protected function getFormSchema(): array{
        return [
            Wizard::make([
                Step::make('المعلومات الشخصية والحساب والسكن')->schema([
                        // إنشاء الحساب
                        TextInput::make('name')->label('الاسم الرباعي')->required(),
                        TextInput::make('email')->label('البريد الإلكتروني')->email()->required()->unique(User::class, 'email'),
                        TextInput::make('password')->label('كلمة المرور')->password()->required()->minLength(8),
                        TextInput::make('phone')->label('رقم الهاتف')->required(),

                        // معلومات شخصية
                        TextInput::make('mother_name')->label('اسم الأم'),
                        TextInput::make('national_id')->label('رقم الهوية'),
                        DatePicker::make('date_of_birth')->label('تاريخ الميلاد'),
                        TextInput::make('place_of_birth')->label('مكان الميلاد'),

                        // معلومات السكن
                        TextInput::make('address_province')->label('المحافظة')->required(),
                        TextInput::make('address_district')->label('القضاء')->required(),
                        TextInput::make('address_subdistrict')->label('الناحية')->required(),
                        TextInput::make('address_details')->label('تفاصيل العنوان')->required(),
                    ])->columns(2),

                Step::make('الانتسابات')->schema([
                    // ❶ اختيار القطاع مرة واحدة
                    Select::make('employment_sector')
                        ->label('هل أنت موظّف؟ القطاع')
                        ->options(['public' => 'عام', 'private' => 'خاص'])
                        ->default('private')
                        ->required()
                        ->live(),

                    // ❷ مجموعة الانتسابات (متعددة)
                    Repeater::make('affiliations')
                        ->label('انتسابات الجهات (يمكن إضافة أكثر من جهة)')
                        ->schema([
                            // عند القطاع الخاص فقط نسمح بالاختيار بين اتحاد/مؤسسة
                            Select::make('kind')
                                ->label('نوع الجهة')
                                ->options(['federation' => 'اتحاد', 'institution' => 'مؤسسة'])
                                ->live()
                                ->visible(fn (Get $get) => $get('../../employment_sector') === 'private')
                                ->required(fn (Get $get) => $get('../../employment_sector') === 'private'),

                            // الاتحادات (للقطاع الخاص عند اختيار نوع = اتحاد)
                            Select::make('federation_id')
                                ->label('الاتحاد')
                                ->options(fn () =>
                                Organization::whereIn('type',['general_union','sub_union','trade_union'])
                                    ->orderBy('name')->pluck('name', 'id')->toArray()
                                )
                                ->searchable()->preload()->live()
                                ->visible(fn (Get $get) =>
                                    $get('../../employment_sector') === 'private'
                                    && $get('kind') === 'federation'
                                )
                                ->required(fn (Get $get) =>
                                    $get('../../employment_sector') === 'private'
                                    && $get('kind') === 'federation'
                                ),

                            // النقابات التابعة للاتحاد
                            Toggle::make('is_union_staff')
                                ->label('أنا موظف/إداري في الاتحاد (الانتساب مباشرة للاتحاد)')
                                ->inline(false)
                                ->default(false)
                                ->visible(fn (Get $get) =>
                                    $get('../../employment_sector') === 'private'
                                    && $get('kind') === 'federation'
                                )
                                ->live(),

// النقابات التابعة للاتحاد — مطلوبة إلا إذا كان الموظف اتحاد
                            Select::make('union_id')
                                ->label('النقابة')
                                ->options(fn (Get $get) =>
                                $get('federation_id')
                                    ? Organization::where('type', 'guild')
                                    ->where('organization_id', $get('federation_id'))
                                    ->orderBy('name')->pluck('name', 'id')->toArray()
                                    : []
                                )
                                ->searchable()->preload()
                                ->visible(fn (Get $get) =>
                                    $get('../../employment_sector') === 'private'
                                    && $get('kind') === 'federation'
                                    && ! (bool) $get('is_union_staff')
                                )
                                ->required(fn (Get $get) =>
                                    $get('../../employment_sector') === 'private'
                                    && $get('kind') === 'federation'
                                    && ! (bool) $get('is_union_staff')
                                ),

                            // مؤسسات:
                            // - للقطاع العام: مؤسسات code = OR1 فقط
                            // - للقطاع الخاص: مؤسسات رئيسية فقط (نفترض parent_id IS NULL)
                            Select::make('institution_id')
                                ->label('المؤسسة')
                                ->options(function (Get $get) {
                                    if ($get('../../employment_sector') === 'public') {
                                        return Organization::where('type', 'organization')
                                            ->orderBy('name')->pluck('name', 'id')->toArray();
                                    }
                                    // قطاع خاص: مؤسسات رئيسية فقط
                                    return Organization::where('type', 'organization')
                                        ->whereNull('organization_id')   // عدّلها إذا كان لديك معيار آخر "رئيسية"
                                        ->orderBy('name')->pluck('name', 'id')->toArray();
                                })
                                ->searchable()->preload()
                                ->visible(fn (Get $get) =>
                                    // تظهر دائمًا في القطاع العام،
                                    // وفي القطاع الخاص تظهر فقط لو نوع الجهة = مؤسسة
                                    $get('../../employment_sector') === 'public'
                                    || ($get('../../employment_sector') === 'private' && $get('kind') === 'institution')
                                )
                                ->required(fn (Get $get) =>
                                    $get('../../employment_sector') === 'public'
                                    || ($get('../../employment_sector') === 'private' && $get('kind') === 'institution')
                                ),

                            // المهنة (إجباري) + التخصص (اختياري) لكل انتساب
                            Select::make('profession_id')
                                ->label('المهنة')
                                ->options(fn () => Profession::orderBy('name')->pluck('name', 'id')->toArray())
                                ->searchable()->preload()->live()
                                ->required(),

                            Select::make('specialization_id')
                                ->label('التخصص (اختياري)')
                                ->options(fn (Get $get) =>
                                $get('profession_id')
                                    ? Specialization::where('profession_id', $get('profession_id'))
                                    ->orderBy('name')->pluck('name', 'id')->toArray()
                                    : []
                                )
                                ->searchable()->preload()
                                ->nullable(),
                            DatePicker::make('joined_at')->label('تاريخ الانضمام')->default(now()),
                            TextInput::make('affiliation_fee')
                                ->label('رسوم الانتساب (اختياري)')
                                ->numeric()
                                ->minValue(0)
                                ->default(0),
                        ])
                        ->defaultItems(1)
                        ->minItems(1)
                        ->columns(2)
                        ->reorderable(false)
                        ->cloneable(false),
                ])->columns(1),

                Step::make('الباقات والعروض')->schema([
                    Repeater::make('offerings')
                        ->label('اختر الباقات / العروض (يمكن إضافة أكثر من عنصر)')
                        // مهم: نوفّر عنصر افتراضي بكل المفاتيح حتى لا تظهر أخطاء entangle
                        ->default([
                            [
                                'partner_id'           => null,
                                'package_id'           => null,
                                'partner_offering_id'  => null,
                                'price'                => null,
                                'details'              => null,
                            ],
                        ])
                        ->minItems(1)
                        ->columns(2)
                        ->reorderable(false)
                        ->cloneable(false)
                        ->schema([

                            // 1) الشريك (شركة التأمين)
                            Radio::make('partner_id')
                                ->label('الشريك (شركة التأمين)')
                                ->columns(2)
                                ->required()
                                ->live()
                                ->options(function () {
                                    $today = now()->toDateString();

                                    return Organization::where('type', OrganizationType::INSURANCE_COMPANY)
                                        ->whereHas('partnerOfferings', fn ($q) =>
                                        $q->where(fn ($qq) => $qq->whereNull('contract_start')
                                            ->orWhereDate('contract_start', '<=', $today))
                                            ->where(fn ($qq) => $qq->whereNull('contract_end')
                                                ->orWhereDate('contract_end', '>=', $today))
                                        )
                                        ->orderBy('name')
                                        ->pluck('name', 'id')
                                        ->mapWithKeys(fn ($label, $id) => [(string) $id => $label]) // مفاتيح نصية للراديو
                                        ->all();
                                })
                                // نعرض/نحفظ كسلسلة -> عدد
                                ->formatStateUsing(fn ($state) => filled($state) ? (string) $state : null)
                                ->dehydrateStateUsing(fn ($state) => filled($state) ? (int) $state : null)
                                ->afterStateUpdated(function (Set $set) {
                                    $set('package_id', null);
                                    $set('partner_offering_id', null);
                                    $set('price', null);
                                    $set('details', null);
                                })
                                ->helperText('تظهر فقط شركات التأمين التي لديها عروض فعّالة اليوم.'),

                            // 2) الباقة (يظهر بعد اختيار الشريك)
                            Radio::make('package_id')
                                ->label('الباقة')
                                ->columns(2)
                                ->live()
                                ->hidden(fn (Get $get) => blank($get('partner_id')))
                                ->required(fn (Get $get) => filled($get('partner_id')))
                                ->options(function (Get $get) {
                                    $partnerId = (int) $get('partner_id');
                                    if (!$partnerId) return [];

                                    $today = now()->toDateString();

                                    $activePackageIds = PartnerOffering::query()
                                        ->where('organization_id', $partnerId)
                                        ->where(fn ($q) => $q->whereNull('contract_start')->orWhereDate('contract_start', '<=', $today))
                                        ->where(fn ($q) => $q->whereNull('contract_end')->orWhereDate('contract_end', '>=', $today))
                                        ->pluck('package_id')
                                        ->unique()
                                        ->values();

                                    if ($activePackageIds->isEmpty()) return [];

                                    return Package::whereIn('id', $activePackageIds)
                                        ->orderBy('name')
                                        ->pluck('name', 'id')
                                        ->mapWithKeys(fn ($label, $id) => [(string) $id => $label])
                                        ->all();
                                })
                                ->formatStateUsing(fn ($state) => filled($state) ? (string) $state : null)
                                ->dehydrateStateUsing(fn ($state) => filled($state) ? (int) $state : null)
                                ->afterStateUpdated(function (Set $set) {
                                    $set('partner_offering_id', null);
                                    $set('price', null);
                                    $set('details', null);
                                }),

                            // 3) العرض (PartnerOffering) — بعد اختيار الشريك والباقة
                            Radio::make('partner_offering_id')
                                ->label('العرض')
                                ->columns(1)
                                ->live()
                                ->hidden(fn (Get $get) => blank($get('partner_id')) || blank($get('package_id')))
                                ->required(fn (Get $get) => filled($get('partner_id')) && filled($get('package_id')))
                                ->options(function (Get $get) {
                                    $partnerId = (int) $get('partner_id');
                                    $packageId = (int) $get('package_id');
                                    if (!$partnerId || !$packageId) return [];

                                    $today = now()->toDateString();

                                    return PartnerOffering::query()
                                        ->where('organization_id', $partnerId)
                                        ->where('package_id', $packageId)
                                        ->where(fn ($q) => $q->whereNull('contract_start')->orWhereDate('contract_start', '<=', $today))
                                        ->where(fn ($q) => $q->whereNull('contract_end')->orWhereDate('contract_end', '>=', $today))
                                        ->orderBy('id')
                                        ->get()
                                        ->mapWithKeys(fn ($po) => [
                                            (string) $po->id => "عقد #{$po->id} — " . number_format((float) $po->price) . " IQD",
                                        ])
                                        ->all();
                                })
                                ->formatStateUsing(fn ($state) => filled($state) ? (string) $state : null)
                                ->dehydrateStateUsing(fn ($state) => filled($state) ? (int) $state : null)
                                ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                    if ($state) {
                                        $po = PartnerOffering::find((int) $state);
                                        $set('price', (string) ($po?->price ?? 0));
                                        // استخدم تفاصيل الباقة أو أي حقل مناسب
                                        $summary = $po?->package?->description ?? null;
                                        $set('details', $summary ? (string) $summary : null);
                                    } else {
                                        $set('price', null);
                                        $set('details', null);
                                    }
                                }),

                            // السعر — يظهر بعد اختيار العرض فقط
                            TextInput::make('price')
                                ->label('السعر')
                                ->disabled()
                                ->dehydrated(false)
                                ->hidden(fn (Get $get) => blank($get('partner_offering_id'))),

                            // تفاصيل العرض — تظهر بعد اختيار العرض
                            Placeholder::make('details_preview')
                                ->label('تفاصيل التغطية / الخدمات في العرض')
                                ->content(function (Get $get) {
                                    $html = (string) ($get('partner_offering_id') ? ($get('details') ?? '') : '');

                                    if ($html === '') {
                                        return new HtmlString('<span class="text-gray-500">لا توجد تفاصيل متاحة.</span>');
                                    }

                                    // Tailwind Typography (prose) makes the HTML very readable.
                                    // If you don’t use the plugin, keep the wrapper — it still looks fine.
                                    return new HtmlString(
                                        '<div class="prose max-w-none rtl:text-right dark:prose-invert">'.$html.'</div>'
                                    );
                                })
                                ->columnSpanFull()
                                ->hidden(fn (Get $get) => blank($get('partner_offering_id'))),
                        ]),
                ])->columns(1),

                Step::make('الخدمات الإضافية (اختياري)')->schema([
                    Repeater::make('service_requests')
                        ->label('طلبات خدمات إضافية (اختياري) — أضف عنصرًا لكل خدمة تريدها')
                        ->default([])                 // لا عناصر افتراضيًا
                        ->minItems(0)
                        ->columns(2)
                        ->reorderable(false)
                        ->schema([

                            // اختر الخدمة (فعّالة فقط)
                            Radio::make('service_id')
                                ->label('الخدمة')
                                ->columns(2)
                                ->live()
                                ->required()
                                ->options(fn () =>
                                Service::query()
                                    ->where('is_active', true)
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->mapWithKeys(fn($label, $id) => [(string)$id => $label])
                                    ->all()
                                )
                                ->formatStateUsing(fn ($state) => filled($state) ? (string) $state : null)
                                ->dehydrateStateUsing(fn ($state) => filled($state) ? (int) $state : null)
                                ->afterStateUpdated(function (Set $set) {
                                    // افرغ الحقول عند تغيير الخدمة
                                    $set('fields', []);
                                })
                                ->helperText('يعرض الخدمات الفعّالة فقط.'),

                            // معلومات سريعة عن الخدمة المختارة
                            Placeholder::make('service_info')
                                ->label('معلومات الخدمة')
                                ->content(function (Get $get) {
                                    $id = (int) $get('service_id');
                                    if (!$id) return 'اختر خدمة لعرض التفاصيل.';
                                    $svc = Service::find($id);
                                    if (!$svc) return 'الخدمة غير موجودة.';
                                    $price = number_format((float) ($svc->base_price ?? 0)) . ' IQD';
                                    return "السعر الأساسي: {$price}" . ($svc->description ? " — الوصف: {$svc->description}" : '');
                                })
                                ->columnSpanFull()
                                ->visible(fn(Get $get) => filled($get('service_id'))),

                            // الحقول الديناميكية القادمة من request_schema
                            Fieldset::make('بيانات الطلب')
                                ->schema(fn (Get $get) => $this->buildServiceRequestFields($get('service_id')))
                                ->visible(fn (Get $get) => filled($get('service_id')))
                                ->columnSpanFull(),
                        ]),
                ])->columns(1),

                Step::make('العائلة (اختياري)')->schema([
                        Toggle::make('has_family')
                            ->label('هل لديك أفراد عائلة لإضافتهم؟')
                            ->inline(false)
                            ->reactive()
                            ->default(false),

                        Repeater::make('family_members')
                            ->label('أفراد العائلة')
                            ->hidden(fn (Get $get) => ! $get('has_family'))
                            ->minItems(1)
                            ->columns(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('الاسم')
                                    ->required(),

                                Select::make('relation')
                                    ->label('صلة القرابة')
                                    ->options([
                                        'spouse'   => 'زوج/زوجة',
                                        'son'      => 'ابن',
                                        'daughter' => 'ابنة',
                                        'parent'   => 'والد/والدة',
                                        'other'    => 'أخرى',
                                    ])
                                    ->required(),

                                TextInput::make('email')
                                    ->label('البريد الإلكتروني')
                                    ->email()
                                    ->required()
                                    ->unique(ignorable: null, table: 'users', column: 'email'),

                                TextInput::make('password')
                                    ->label('كلمة المرور')
                                    ->password()
                                    ->required()
                                    ->minLength(6),
                            ]),

                        Fieldset::make('عاملون مرتبطون (اختياري)')
                            ->schema([
                                Toggle::make('has_related_workers')
                                    ->label('هل تريد إضافة عاملين مرتبطين بهذا العامل؟')
                                    ->reactive()
                                    ->default(false),

                                // pick EXISTING workers (already in users table)
                                Select::make('related_workers_existing')
                                    ->label('اختيار عاملين موجودين')
                                    ->helperText('ابحث واختر عاملين موجودين مسبقًا لربطهم بهذا العامل.')
                                    ->multiple()
                                    ->searchable()
                                    ->preload()
                                    ->hidden(fn (Get $get) => ! $get('has_related_workers'))
                                    ->options(
                                        User::query()
                                            ->orderBy('name')
                                            ->get()
                                            ->mapWithKeys(fn ($u) => [$u->id => "{$u->name} ({$u->email})"])
                                            ->all()
                                    ),

                                // create NEW related workers on the fly
                                Repeater::make('related_workers_new')
                                    ->label('إضافة عاملين جدد (إنشاء حسابات جديدة)')
                                    ->hidden(fn (Get $get) => ! $get('has_related_workers'))
                                    ->minItems(0)
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('name')->label('الاسم')->required(),
                                        TextInput::make('email')->label('البريد الإلكتروني')->email()->required(),
                                        TextInput::make('password')->label('كلمة المرور')->password()->required()->minLength(6),
                                    ]),
                            ])
                            ->columns(1),

                    ])->columns(1),

                Step::make('الملخّص المالي والدفع')->schema([

                    Section::make('ملخّص المبالغ المستحقة')
                        ->description('الحسابات تتحدّث تلقائيًا بناءً على اختياراتك في الخطوات السابقة.')
                        ->schema([
                            Grid::make(['default' => 4])->schema([

                                // رسوم الانتساب
                                Placeholder::make('calc_affiliation_fees')
                                    ->label('رسوم الانتساب')
                                    ->content(function (Get $get) {
                                        $sum = collect($get('affiliations') ?? [])
                                            ->sum(fn ($row) => (float) ($row['affiliation_fee'] ?? 0));
                                        return number_format($sum, 2).' IQD';
                                    })
                                    ->extraAttributes(['class' => 'fi-placeholder'])
                                    ->inlineLabel(false),

                                // مجموع الباقات
                                Placeholder::make('calc_offerings_total')
                                    ->label('مجموع الباقات')
                                    ->content(function (Get $get) {
                                        $ids = collect($get('offerings') ?? [])
                                            ->pluck('partner_offering_id')->filter()->values();
                                        $sum = $ids->isNotEmpty()
                                            ? (float) PartnerOffering::whereIn('id', $ids)->sum('price')
                                            : 0;
                                        return number_format($sum, 2).' IQD';
                                    })
                                    ->extraAttributes(['class' => 'fi-placeholder'])
                                    ->inlineLabel(false),

                                // مجموع الخدمات
                                Placeholder::make('calc_services_total')
                                    ->label('مجموع الخدمات')
                                    ->content(function (Get $get) {
                                        $ids = collect($get('service_requests') ?? [])
                                            ->pluck('service_id')->filter()->values();
                                        $sum = ($ids->isNotEmpty() && class_exists(Service::class))
                                            ? (float) Service::whereIn('id', $ids)->sum('base_price')
                                            : 0;
                                        return number_format($sum, 2).' IQD';
                                    })
                                    ->extraAttributes(['class' => 'fi-placeholder'])
                                    ->inlineLabel(false),

                                // الإجمالي قبل الخصم
                                Placeholder::make('calc_subtotal')
                                    ->label('الإجمالي قبل الخصم')
                                    ->content(function (Get $get) {
                                        $aff = collect($get('affiliations') ?? [])
                                            ->sum(fn ($row) => (float) ($row['affiliation_fee'] ?? 0));

                                        $offerIds = collect($get('offerings') ?? [])
                                            ->pluck('partner_offering_id')->filter()->values();
                                        $offerSum = $offerIds->isNotEmpty()
                                            ? (float) PartnerOffering::whereIn('id', $offerIds)->sum('price')
                                            : 0;

                                        $serviceIds = collect($get('service_requests') ?? [])
                                            ->pluck('service_id')->filter()->values();
                                        $serviceSum = ($serviceIds->isNotEmpty() && class_exists(Service::class))
                                            ? (float) Service::whereIn('id', $serviceIds)->sum('base_price')
                                            : 0;

                                        return number_format($aff + $offerSum + $serviceSum, 2).' IQD';
                                    })
                                    ->extraAttributes(['class' => 'fi-placeholder'])
                                    ->inlineLabel(false),
                            ]),

                            // مربع الخصم
                            Fieldset::make('الخصم')
                                ->schema([
                                    Radio::make('discount_type')
                                        ->label('نوع الخصم')
                                        ->options([
                                            'none'    => 'بدون خصم',
                                            'percent' => 'نسبة مئوية %',
                                            'fixed'   => 'مبلغ ثابت',
                                        ])
                                        ->default('none')
                                        ->live()
                                        ->inline()
                                        ->columnSpanFull(),

                                    TextInput::make('discount_value')
                                        ->label(fn (Get $get) => $get('discount_type') === 'percent' ? 'قيمة الخصم (%)' : 'قيمة الخصم (IQD)')
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue(fn (Get $get) => $get('discount_type') === 'percent' ? 100 : null)
                                        ->hidden(fn (Get $get) => $get('discount_type') === 'none')
                                        ->default(0)
                                        ->live(onBlur: true),

                                    Select::make('discount_funded_by')
                                        ->label('الجهة المموِّلة للخصم')
                                        ->options([
                                            'platform' => 'المنصّة',
                                            'partner'  => 'الشريك',
                                            'host'     => 'الجهة المضيفة',
                                            'shared'   => 'مناصفة/مشترك',
                                        ])
                                        ->default('platform')
                                        ->hidden(fn (Get $get) => $get('discount_type') === 'none'),
                                ])
                                ->columns(2),

                            // عرض “الخصم الفعّال” و”الإجمالي بعد الخصم” (عرض فقط)
                            Grid::make(['default' => 4])->schema([
                                Placeholder::make('calc_discount_effective')
                                    ->label('الخصم المُطبّق')
                                    ->content(function (Get $get) {
                                        // احسب Subtotal
                                        $aff = collect($get('affiliations') ?? [])
                                            ->sum(fn ($row) => (float) ($row['affiliation_fee'] ?? 0));

                                        $offerIds = collect($get('offerings') ?? [])
                                            ->pluck('partner_offering_id')->filter()->values();
                                        $offerSum = $offerIds->isNotEmpty()
                                            ? (float) PartnerOffering::whereIn('id', $offerIds)->sum('price')
                                            : 0;

                                        $serviceIds = collect($get('service_requests') ?? [])
                                            ->pluck('service_id')->filter()->values();
                                        $serviceSum = ($serviceIds->isNotEmpty() && class_exists(Service::class))
                                            ? (float) Service::whereIn('id', $serviceIds)->sum('base_price')
                                            : 0;

                                        $subtotal = $aff + $offerSum + $serviceSum;

                                        $dtype  = $get('discount_type') ?? 'none';
                                        $dval   = (float) ($get('discount_value') ?? 0);

                                        $applied = 0.0;
                                        if ($dtype === 'percent') {
                                            $pct = max(0, min(100, $dval));
                                            $applied = round($subtotal * $pct / 100, 2);
                                        } elseif ($dtype === 'fixed') {
                                            $applied = max(0, min($subtotal, $dval));
                                        }
                                        return number_format($applied, 2).' IQD';
                                    })
                                    ->extraAttributes(['class' => 'fi-placeholder'])
                                    ->inlineLabel(false),

                                Placeholder::make('calc_net_total')
                                    ->label('الإجمالي بعد الخصم')
                                    ->content(function (Get $get) {
                                        // Subtotal
                                        $aff = collect($get('affiliations') ?? [])
                                            ->sum(fn ($row) => (float) ($row['affiliation_fee'] ?? 0));

                                        $offerIds = collect($get('offerings') ?? [])
                                            ->pluck('partner_offering_id')->filter()->values();
                                        $offerSum = $offerIds->isNotEmpty()
                                            ? (float) PartnerOffering::whereIn('id', $offerIds)->sum('price')
                                            : 0;

                                        $serviceIds = collect($get('service_requests') ?? [])
                                            ->pluck('service_id')->filter()->values();
                                        $serviceSum = ($serviceIds->isNotEmpty() && class_exists(Service::class))
                                            ? (float) Service::whereIn('id', $serviceIds)->sum('base_price')
                                            : 0;

                                        $subtotal = $aff + $offerSum + $serviceSum;

                                        // Discount
                                        $dtype  = $get('discount_type') ?? 'none';
                                        $dval   = (float) ($get('discount_value') ?? 0);

                                        $applied = 0.0;
                                        if ($dtype === 'percent') {
                                            $pct = max(0, min(100, $dval));
                                            $applied = round($subtotal * $pct / 100, 2);
                                        } elseif ($dtype === 'fixed') {
                                            $applied = max(0, min($subtotal, $dval));
                                        }

                                        $net = max(0, $subtotal - $applied);
                                        return number_format($net, 2).' IQD';
                                    })
                                    ->extraAttributes(['class' => 'fi-placeholder'])
                                    ->inlineLabel(false),
                            ]),
                        ]),

                    Fieldset::make('الدفع (اختياري الآن)')
                        ->schema([
                            Toggle::make('take_payment_now')
                                ->label('تحصيل المبلغ الآن؟')
                                ->default(false)
                                ->live(),

                            Select::make('payment_method')
                                ->label('طريقة الدفع')
                                ->options([
                                    'cash'     => 'نقدًا',
                                    'pos'      => 'بطاقة / POS',
                                    'zaincash' => 'زين كاش',
                                    'bank'     => 'تحويل بنكي',
                                ])
                                ->required(fn (Get $get) => (bool) $get('take_payment_now'))
                                ->hidden(fn (Get $get)   => ! (bool) $get('take_payment_now')),

                            // المبلغ المُحصَّل الآن (اختياري — إن لم تُدخله سنرسل 0 للخدمة)
                            TextInput::make('amount_taken_now')
                                ->label('المبلغ المُحصّل الآن (اختياري)')
                                ->numeric()
                                ->minValue(0)
                                ->hidden(fn (Get $get) => ! (bool) $get('take_payment_now')),
                        ]),

                ])->columns(1),

            ]),
        ];
    }

    protected function buildServiceRequestFields($serviceId): array{
        $svc = $serviceId ? Service::find((int) $serviceId) : null;
        $schema = $svc?->request_schema ?? [];
        if (!is_array($schema) || empty($schema)) {
            return [ Placeholder::make('no_fields')->content('لا توجد حقول مطلوبة لهذه الخدمة.')->columnSpanFull() ];
        }

        $fields = [];

        foreach ($schema as $i => $f) {
            $key      = $f['key']    ?? ('field_'.$i);
            $label    = $f['label']  ?? ucfirst($key);
            $type     = $f['type']   ?? 'text';
            $required = (bool)($f['required'] ?? false);
            $options  = $f['options'] ?? []; // select/radio

            $name = "fields.$key"; // نخزن كل مدخلات الخدمة في مصفوفة fields

            switch ($type) {
                case 'number':
                    $fields[] = TextInput::make($name)->label($label)->numeric()->required($required);
                    break;

                case 'date':
                    $fields[] = DatePicker::make($name)->label($label)->required($required);
                    break;

                case 'textarea':
                    $fields[] = Textarea::make($name)->label($label)->rows(3)->required($required);
                    break;

                case 'select':
                    $fields[] = Select::make($name)
                        ->label($label)
                        ->options(is_array($options) ? $options : [])
                        ->searchable()
                        ->required($required);
                    break;

                case 'radio':
                    $fields[] = Radio::make($name)
                        ->label($label)
                        ->options(is_array($options) ? $options : [])
                        ->columns(2)
                        ->required($required);
                    break;

                case 'boolean':
                case 'toggle':
                    $fields[] = Toggle::make($name)->label($label)->required($required);
                    break;

                default: // text
                    $fields[] = TextInput::make($name)->label($label)->required($required);
            }
        }

        // يمكنك توزيعها على عمودين:
        foreach ($fields as $idx => $comp) {
            $fields[$idx] = $comp->columnSpan(1);
        }

        return $fields;
    }
    public function submit(): void{
        $this->validate([
            // account
            'email'    => ['required','email','unique:users,email'],
            'password' => ['required','string','min:8'],
            'name'     => ['required','string'],
            'phone'    => ['required','string'],

            // address
            'address_province'    => ['required','string','max:255'],
            'address_district'    => ['required','string','max:255'],
            'address_subdistrict' => ['required','string','max:255'],
            'address_details'     => ['required','string','max:255'],
            'extra_data'          => ['nullable','array'],
            'image'               => ['nullable','string','max:255'],

            // Affiliation source
            'issuer_employee_id'  => ['required','integer','exists:employees,id'],

            // sector
            'employment_sector'   => ['required','in:public,private'],

            // affiliation
            'affiliations'                     => ['required','array','min:1'],
            'affiliations.*.is_union_staff'   => ['nullable','boolean'],
            'affiliations.*.kind'              => ['nullable','in:federation,institution'],
            'affiliations.*.federation_id'     => ['nullable','integer','exists:organizations,id'],
            'affiliations.*.union_id'          => ['nullable','integer','exists:organizations,id'],
            'affiliations.*.institution_id'    => ['nullable','integer','exists:organizations,id'],
            'affiliations.*.profession_id'     => ['required','integer','exists:professions,id'],
            'affiliations.*.specialization_id' => ['nullable','integer','exists:specializations,id'],
            'affiliations.*.affiliation_fee'   => ['nullable','numeric','min:0'],
            'affiliations.*.joined_at'         => ['nullable','date'],

            // offers / packages
            'offerings'                       => ['required','array','min:1'],
            'offerings.*.partner_offering_id' => ['required','integer','exists:partner_offerings,id'],

            // services
            'service_requests'              => ['nullable','array'],
            'service_requests.*.service_id' => ['required','integer','exists:services,id'],

            // family
            'has_family'                => ['boolean'],
            'family_members'            => ['nullable','array'],
            'family_members.*.name'     => ['required_with:family_members','string'],
            'family_members.*.relation' => ['required_with:family_members','in:spouse,son,daughter,parent,other'],
            'family_members.*.email'    => ['required_with:family_members','email','unique:users,email'],
            'family_members.*.password' => ['required_with:family_members','string','min:6'],

            //  Workers
            'has_related_workers'            => ['boolean'],
            'related_workers_existing'       => ['nullable','array'],
            'related_workers_existing.*'     => ['integer','exists:users,id'],
            'related_workers_new'            => ['nullable','array'],
            'related_workers_new.*.name'     => ['required_with:related_workers_new','string'],
            'related_workers_new.*.email'    => ['required_with:related_workers_new','email','unique:users,email'],
            'related_workers_new.*.password' => ['required_with:related_workers_new','string','min:6'],

            // payment
            'take_payment_now'   => ['boolean'],
            'payment_method'     => ['nullable','in:cash,pos,zaincash,bank'],
            'amount_taken_now'   => ['nullable','numeric','min:0'],
            'discount_type'      => ['required','in:none,percent,fixed'],
            'discount_value'     => ['nullable','numeric','min:0'],
            'discount_funded_by' => ['required_if:discount_type,percent,fixed','in:platform,partner,host,shared'],

        ]);

        try {
            DB::beginTransaction();

            // 1) المستخدم الأساسي + الدور + الملف الشخصي + المحفظة
            $user = User::create([
                'name'     => $this->name,
                'email'    => $this->email,
                'password' => Hash::make($this->password),
            ]);

            $memberRole = Role::firstOrCreate(['name' => 'منتسب', 'guard_name' => 'web']);
            $user->assignRole($memberRole);

            $user->userProfiles()->create([
                'user_id'            => $user->id,
                'name'               => $this->name,
                'mother_name'        => $this->mother_name,
                'national_id'        => $this->national_id,
                'date_of_birth'      => $this->date_of_birth,
                'place_of_birth'     => $this->place_of_birth,
                'phone'              => $this->phone,
                'address_province'   => $this->address_province,
                'address_district'   => $this->address_district,
                'address_subdistrict'=> $this->address_subdistrict,
                'address_details'    => $this->address_details,
                'extra_data'         => $this->extra_data ?? [],
                'image'              => $this->image ?? '',
            ]);

            Wallet::updateOrCreate(
                ['walletable_type' => User::class, 'walletable_id' => $user->id],
                ['user_id' => $user->id, 'balance' => 0, 'currency' => 'IQD']
            );

            // 2) prepare Payload affiliation
            $affiliationsPayload = [];
            foreach ($this->affiliations as $row) {
                $orgId = null;
                if ($this->employment_sector === 'public') {
                    $orgId = $row['institution_id'] ?? null;
                } else {
                if (($row['kind'] ?? null) === 'federation') {
                    $isStaff = (bool) ($row['is_union_staff'] ?? false);
                    $orgId   = $isStaff
                        ? ($row['federation_id'] ?? null)   // موظف اتحاد → الانتساب للاتحاد نفسه
                        : ($row['union_id']      ?? null);  // عضو عادي → انتساب للنقابة التابعة
                } else {
                    $orgId = $row['institution_id'] ?? null;
                }
            }

                if (!$orgId) {
                    throw ValidationException::withMessages([
                        'affiliations' => 'يجب اختيار جهة صحيحة لكل انتساب.',
                    ]);
                }

                $affiliationsPayload[] = [
                    'organization_id' => (int) $orgId,
                    'affiliation_fee' => (float) ($row['affiliation_fee'] ?? 0),
                    'joined_at'       => $row['joined_at'] ?? now()->toDateString(),
                    'status'          => 'pending',
                    'profession_id'     => (int) ($row['profession_id'] ?? 0),
                    'specialization_id' => $row['specialization_id'] ?? null,
                ];
            }

            // 3) Payload الباقات/العروض
            $offeringsPayload = [];
            foreach ($this->offerings as $row) {
                $poId = (int) ($row['partner_offering_id'] ?? 0);
                if ($poId > 0) {
                    $offeringsPayload[] = [
                        'partner_offering_id' => $poId,
                    ];
                }
            }

            // 4) Payload services + create UserService  (optional)
            $servicesPayload = [];
            foreach ($this->service_requests as $sr) {
                $svc = Service::find($sr['service_id'] ?? null);
                if (!$svc) { continue; }

                $price = (float) ($svc->base_price ?? 0);
                $servicesPayload[] = [
                    'service_id'  => (int) $svc->id,
                    'description' => 'طلب خدمة: ' . $svc->name,
                    'price'       => $price,
                ];

                $currentEmployeeId = Employee::where('user_id', auth()->id())->value('id');

                $user->userServices()->create([
                    'service_id'   => $svc->id,
                    'status'       => 'applied',
                    'submitted_at' => now(),
                    'user_id'      => $user->id,
                    'notes'        => '',                 // <- use notes
                    'processed_by' => $currentEmployeeId, // <- use processed_by (employee id)
                    'form_data'    => $sr['fields'] ?? [],// <- persist dynamic fields
                ]);


            }

            // 5) العائلة: تُنشأ سجلات users مباشرة مع parent_id + family_relation
            if ($this->has_family && !empty($this->family_members)) {
                foreach ($this->family_members as $fm) {
                    $famUser = User::create([
                        'name'     => $fm['name'],
                        'email'    => $fm['email'],
                        'password' => Hash::make($fm['password']),
                    ]);

                    // الربط بالعامل الأساسي
                    $famUser->parent_id       = $user->id;
                    $famUser->family_relation = $fm['relation']; // spouse|son|daughter|parent|other
                    $famUser->save();

                    // (اختياري) نفس الدور
                    $famUser->assignRole($memberRole);

                    // ملف شخصي مبسّط
                    $famUser->userProfiles()->create([
                        'user_id'            => $famUser->id,
                        'name'               => $fm['name'],
                        'phone'              => null,
                        'address_province'   => $this->address_province,
                        'address_district'   => $this->address_district,
                        'address_subdistrict'=> $this->address_subdistrict,
                        'address_details'    => $this->address_details,
                        'extra_data'         => ['relation_to' => $user->id, 'relation' => $fm['relation']],
                        'image'              => '',
                    ]);
                }
            }

            // 6) العمال المرتبطون: جدول related_workers (user_id, related_user_id)
            if ($this->has_related_workers) {
                // موجودون
                foreach (($this->related_workers_existing ?? []) as $rid) {
                    DB::table('related_workers')->updateOrInsert(
                        ['user_id' => (int) $user->id, 'related_user_id' => (int) $rid],
                        []
                    );
                }
                // جدد
                foreach (($this->related_workers_new ?? []) as $rw) {
                    $newU = User::create([
                        'name'     => $rw['name'],
                        'email'    => $rw['email'],
                        'password' => Hash::make($rw['password']),
                    ]);
                    $newU->assignRole($memberRole);

                    $newU->userProfiles()->create([
                        'user_id'            => $newU->id,
                        'name'               => $rw['name'],
                        'phone'              => null,
                        'address_province'   => $this->address_province,
                        'address_district'   => $this->address_district,
                        'address_subdistrict'=> $this->address_subdistrict,
                        'address_details'    => $this->address_details,
                        'extra_data'         => ['related_to' => $user->id],
                        'image'              => '',
                    ]);

                    DB::table('related_workers')->updateOrInsert(
                        ['user_id' => (int) $user->id, 'related_user_id' => (int) $newU->id],
                        []
                    );
                }
            }

            // 7) الـ payload النهائي لخدمة الترحيل المالي
            $payload = [
                'affiliations'       => $affiliationsPayload,
                'offerings'          => $offeringsPayload,
                'services'           => $servicesPayload,

                // الخصم (إن أضفت حقوله لاحقًا بدّل القيم هنا)
                'discount_type'      => $this->discount_type ?? 'none',              // none|percent|fixed
                'discount_value'     => (float) ($this->discount_value ?? 0),
                'discount_funded_by' => $this->discount_funded_by ?? 'platform',

                // الدفع الفوري
                'take_payment_now'   => (bool) ($this->take_payment_now ?? false),
                'payment_method'     => $this->take_payment_now ? ($this->payment_method ?? 'cash') : null,
                'paid_amount'        => $this->take_payment_now ? (float) ($this->amount_taken_now ?? 0) : 0,
            ];

            if (!empty($payload['take_payment_now']) && empty($payload['payment_method'])) {
                throw ValidationException::withMessages([
                    'payment_method' => 'يجب اختيار طريقة الدفع عند تفعيل التحصيل الفوري.',
                ]);
            }

            /** @var \App\Services\AffiliationPostingService $svc */
            $svc = app(AffiliationPostingService::class);
            $invoice = $svc->post($payload, (int) $this->issuer_employee_id, (int) $user->id,$user);

            DB::commit();

            Notification::make()
                ->title('تم إنشاء المستخدم والانتسابات والباقات والخدمات والفاتورة ' . ($invoice->number ?? '#') . ' بنجاح')
                ->success()
                ->send();

            $this->redirect(static::getUrl());

        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            Notification::make()
                ->title('فشل الحفظ')
                ->body(config('app.debug') ? $e->getMessage() : 'حدث خطأ غير متوقع أثناء حفظ الطلب.')
                ->danger()
                ->send();
        }
    }
}
