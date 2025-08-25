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

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use App\Enums\OrganizationType;
use App\Models\Package;
use Filament\Forms\Components\Radio;
class AffiliationWizard extends Page implements HasForms
{
    use InteractsWithForms;
    public ?array $data = [];
    protected static ?string $navigationIcon  = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'تسجيل انتساب جديد';
    protected static ?int    $navigationSort  = 1;
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

    public $affiliations;
    // مجموع ملخص (عرض فقط)
    public float $grand_total = 0;

    public function mount(): void
    {
        $this->issuer_employee_id = Employee::where('user_id', auth()->id())->value('id');
    }

    protected function getFormSchema(): array
    {
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
                    ])
                    ->columns(2),


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
                                )
                                ->required(fn (Get $get) =>
                                    $get('../../employment_sector') === 'private'
                                    && $get('kind') === 'federation'
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
                        ->schema([

                        // 1) PARTNER (insurance company) — radio
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
                                    ->mapWithKeys(fn ($label, $id) => [(string) $id => $label])   // string keys
                                    ->all();
                            })
                            ->formatStateUsing(fn ($state) => filled($state) ? (string) $state : null)
                            ->dehydrateStateUsing(fn ($state) => filled($state) ? (int) $state : null)
                            ->afterStateUpdated(function (Set $set) {
                                $set('package_id', null);
                                $set('partner_offering_id', null);
                                $set('price', null);
                                $set('details', null);
                            })
                            ->helperText('تظهر فقط شركات التأمين التي لديها عروض فعّالة اليوم.'),

                        // 2) PACKAGE — radio, only after partner
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
                                    ->whereNull('deleted_at')
                                    ->where(fn ($q) => $q->whereNull('contract_start')->orWhereDate('contract_start', '<=', $today))
                                    ->where(fn ($q) => $q->whereNull('contract_end')->orWhereDate('contract_end', '>=', $today))
                                    ->pluck('package_id')->unique()->values();

                                if ($activePackageIds->isEmpty()) return [];

                                return Package::whereIn('id', $activePackageIds)
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->mapWithKeys(fn ($label, $id) => [(string) $id => $label])   // string keys
                                    ->all();
                            })
                            ->formatStateUsing(fn ($state) => filled($state) ? (string) $state : null)
                            ->dehydrateStateUsing(fn ($state) => filled($state) ? (int) $state : null)
                            ->afterStateUpdated(function (Set $set) {
                                $set('partner_offering_id', null);
                                $set('price', null);
                                $set('details', null);
                            }),

                        // 3) OFFER (partner_offering) — radio, only after partner & package
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
                                    ->whereNull('deleted_at')
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
                                    // اكتب ملخص التفاصيل الذي تريده
                                    $summary = $po?->package?->details ?? null; // عدِّل إن كان لديك عمود آخر
                                    $set('details', $summary ? (string) $summary : null);
                                } else {
                                    $set('price', null);
                                    $set('details', null);
                                }
                            }),

                        // 4) Price — render only after an offer is chosen
                        TextInput::make('price')
                            ->label('السعر')
                            ->disabled()
                            ->dehydrated(false)
                            ->hidden(fn (Get $get) => blank($get('partner_offering_id'))),

                        // 5) Details — render only after an offer is chosen
                        Textarea::make('details')
                            ->label('تفاصيل التغطية / الخدمات في العرض')
                            ->rows(4)
                            ->disabled()
                            ->dehydrated(false)
                            ->hidden(fn (Get $get) => blank($get('partner_offering_id'))),
                                                ])
                                                ->defaultItems(1)
                                                ->minItems(1)
                                                ->columns(2)
                                                ->reorderable(false)
                                                ->cloneable(false)
                                        ])->columns(1),

                // 5) الباقات (متعددة) + خدمات إضافية
                Step::make('الباقات والخدمات')->schema([
                    Repeater::make('offerings')
                        ->label('باقات الشريك (يمكن إضافة أكثر من باقة)')
                        ->schema([
                            Select::make('partner_offering_id')->label('الباقة')
                                ->options(
                                    PartnerOffering::query()
                                        ->where(function($q){ $q->whereNull('contract_start')->orWhere('contract_start','<=',now()); })
                                        ->where(function($q){ $q->whereNull('contract_end')->orWhere('contract_end','>=',now()); })
                                        ->orderBy('id','desc') // عدّلها إلى name إن توفر
                                        ->pluck('id','id')->toArray()
                                )
                                ->required()->live()
                                ->afterStateUpdated(function(Set $set, $state) {
                                    $price = 0.0;
                                    if ($state) { $po = PartnerOffering::find($state); $price = (float)($po?->price ?? 0); }
                                    $set('price',$price);
                                }),
                            TextInput::make('price')->label('السعر')->disabled()->dehydrated(false),
                        ])
                        ->defaultItems(1)
                        ->minItems(1)
                        ->columns(2)
                        ->reorderable(false)
                        ->cloneable(false),

                    // خدمات إضافية (اختياري)
                    Select::make('services')
                        ->label('خدمات إضافية')
                        ->multiple()
                        ->options(function(){
                            return class_exists(Service::class)
                                ? Service::orderBy('name')->pluck('name','id')->toArray()
                                : [];
                        })
                        ->searchable()
                        ->preload(),
                ])->columns(1),
            ]),
        ];
    }

    public function submit(): void
    {
        $this->validate([
            // حساب
            'email' => ['required','email','unique:users,email'],
            'password' => ['required','string','min:8'],
            'name' => ['required','string'],
            'phone' => ['required','string'],

            // عنوان
            'address_province' => ['required','string','max:255'],
            'address_district' => ['required','string','max:255'],
            'address_subdistrict' => ['required','string','max:255'],
            'address_details' => ['required','string','max:255'],
            'extra_data' => ['nullable','array'],
            'image' => ['nullable','string','max:255'],

            // موظف مُصدر
            'issuer_employee_id' => ['required','integer','exists:employees,id'],

            // انتسابات
            'affiliations' => ['required','array','min:1'],
            'affiliations.*.sector' => ['required','in:public,private'],
            'affiliations.*.kind' => ['required','in:federation,institution'],
            'affiliations.*.federation_id' => ['nullable','integer','exists:organizations,id'],
            'affiliations.*.union_id' => ['nullable','integer','exists:organizations,id'],
            'affiliations.*.institution_id' => ['nullable','integer','exists:organizations,id'],
            'affiliations.*.affiliation_fee' => ['nullable','numeric','min:0'],
            'affiliations.*.joined_at' => ['nullable','date'],
            'affiliations.*.status' => ['required','in:pending,active,rejected'],

            // مهنة
            'profession_id' => ['required','integer','exists:professions,id'],
            'specialization_id' => ['required','integer','exists:specializations,id'],

            // باقات
            'offerings' => ['required','array','min:1'],
            'offerings.*.partner_offering_id' => ['required','integer','exists:partner_offerings,id'],
        ]);

        DB::transaction(function () {
            // 1) المستخدم
            $user = User::create([
                'name' => $this->name,
                'email'=> $this->email,
                'password' => Hash::make($this->password),
            ]);
            $memberRole = Role::firstOrCreate(
                ['name' => 'منتسب', 'guard_name' => $guard],
                []
            );

            $user->assignRole($memberRole);

            // 2) الملف الشخصي
            $user->userProfiles()->create([
                'user_id' => $user->id,
                'name' => $this->name,
                'mother_name' => $this->mother_name,
                'national_id' => $this->national_id,
                'date_of_birth' => $this->date_of_birth,
                'place_of_birth' => $this->place_of_birth,
                'phone' => $this->phone,
                'address_province' => $this->address_province,
                'address_district' => $this->address_district,
                'address_subdistrict' => $this->address_subdistrict,
                'address_details' => $this->address_details,
                'extra_data' => $this->extra_data ?? [],
                'image' => $this->image ?? '',
            ]);

            // 3) محفظة polymorphic
            Wallet::updateOrCreate(
                ['walletable_type' => User::class, 'walletable_id' => $user->id],
                ['user_id'=>$user->id, 'balance'=>0, 'currency'=>'IQD']
            );

            // 4) المهنة/الاختصاص (حالة pending بدلاً من applied حتى لا نصطدم بـ ENUM)
            // نربطها على أول انتساب لاحقًا
            $affForProfession = null;

            // إعداد الحسابات للمحاسبة
            $AR             = '1100'; // ذمم مدينة
            $REV_AFF        = '4100'; // إيراد رسوم انتساب (منصة)
            $REV_PLATFORM   = '4201'; // إيراد منصة من الباقات
            $PAYABLE_PARTNER= '2100'; // مستحقات الشريك (التزام)

            $createdByEmpId = $this->issuer_employee_id;
            if (!$createdByEmpId) {
                throw ValidationException::withMessages(['issuer_employee_id'=>'يجب تحديد الموظّف المنفّذ.']);
            }

            // 5) إنشاء كل الانتسابات + قيد رسوم الانتساب
            $this->grand_total = 0;
            $affiliationRecords = [];

            foreach ($this->affiliations as $row) {
                // تحديد organization_id النهائي
                $organizationId = null;
                if (($row['kind'] ?? null) === 'federation') {
                    // في حالة الاتحاد: النقابة هي الجهة المنتسَب إليها
                    $organizationId = $row['union_id'] ?? null;
                } else { // institution
                    $organizationId = $row['institution_id'] ?? null;
                }

                if (!$organizationId) {
                    throw ValidationException::withMessages(['affiliations'=>'يجب اختيار جهة صحيحة لكل انتساب.']);
                }

                $aff = $user->userAffiliations()->create([
                    'organization_id' => $organizationId,
                    'status'          => $row['status'] ?? 'pending',
                    'joined_at'       => $row['joined_at'] ?? now(),
                ]);
                $affiliationRecords[] = $aff;

                // أول انتساب نربط به المهنة/الاختصاص
                if (!$affForProfession) $affForProfession = $aff;

                // رسوم الانتساب
                $fee = (float)($row['affiliation_fee'] ?? 0);
                if ($fee > 0) {
                    $this->grand_total += $fee;

                    // قيد: مدين AR بالمجموع
                    LedgerEntry::create([
                        'reference_type' => UserAffiliation::class,
                        'reference_id'   => $aff->id,
                        'account_code'   => $AR,
                        'entry_type'     => 'debit',
                        'amount'         => $fee,
                        'description'    => 'رسوم انتساب',
                        'created_by'     => $createdByEmpId,
                        'is_locked'      => false,
                    ]);
                    // قيد: دائن إيراد الانتساب (كلّه للمنصّة)
                    LedgerEntry::create([
                        'reference_type' => UserAffiliation::class,
                        'reference_id'   => $aff->id,
                        'account_code'   => $REV_AFF,
                        'entry_type'     => 'credit',
                        'amount'         => $fee,
                        'description'    => 'إثبات إيراد رسوم انتساب',
                        'created_by'     => $createdByEmpId,
                        'is_locked'      => false,
                    ]);
                }
            }

            // 6) ربط المهنة/الاختصاص على أول انتساب
            if ($affForProfession) {
                $affForProfession->userProfessions()->create([
                    'profession_id'     => $this->profession_id,
                    'specialization_id' => $this->specialization_id,
                    'status'            => 'pending',
                    'applied_at'        => now(),
                    'notes'             => $this->notes,
                ]);
            }

            // 7) إنشاء الباقات المتعددة + قيود التوزيع
            foreach ($this->offerings as $row) {
                $po = PartnerOffering::find($row['partner_offering_id'] ?? null);
                if (!$po) continue;

                $price = (float)($po->price ?? 0);
                $this->grand_total += $price;

                $uo = $user->userOfferings()->create([
                    'status'              => 'applied',
                    'applied_at'          => now(),
                    'partner_offering_id' => $po->id,
                    'notes'               => $this->notes,
                ]);

                // توزيع النِسَب (إن وُجد OfferingDistribution)
                $platformPct = 100.0;
                $partnerPct  = 0.0;

                if (class_exists(OfferingDistribution::class)) {
                    $dist = OfferingDistribution::where('partner_offering_id', $po->id)->first();
                    if ($dist) {
                        // عدِّل أسماء الحقول هنا لو كانت مختلفة لديك
                        $platformPct = (float)($dist->platform_percent ?? $platformPct);
                        $partnerPct  = (float)($dist->partner_percent  ?? $partnerPct);
                        $excess = $platformPct + $partnerPct;
                        if ($excess > 100) { $platformPct = 100; $partnerPct = 0; } // حماية
                    }
                }

                $platformShare = round($price * $platformPct / 100, 2);
                $partnerShare  = round($price * $partnerPct  / 100, 2);
                // ممكن يبقى فرق قروش: نعالجه بإضافة الفرق لحصة المنصّة
                $diff = $price - ($platformShare + $partnerShare);
                if (abs($diff) >= 0.01) $platformShare += $diff;

                // قيد: مدين ذمم بالمبلغ الكامل
                LedgerEntry::create([
                    'reference_type' => UserOffering::class,
                    'reference_id'   => $uo->id,
                    'account_code'   => $AR,
                    'entry_type'     => 'debit',
                    'amount'         => $price,
                    'description'    => 'بيع باقة شريك',
                    'created_by'     => $createdByEmpId,
                    'is_locked'      => false,
                ]);

                // قيد: دائن إيراد المنصّة بحصتها
                if ($platformShare > 0) {
                    LedgerEntry::create([
                        'reference_type' => UserOffering::class,
                        'reference_id'   => $uo->id,
                        'account_code'   => $REV_PLATFORM,
                        'entry_type'     => 'credit',
                        'amount'         => $platformShare,
                        'description'    => 'إيراد المنصّة من الباقة',
                        'created_by'     => $createdByEmpId,
                        'is_locked'      => false,
                    ]);
                }

                // قيد: دائن مستحقات الشريك (التزام) بحصته
                if ($partnerShare > 0) {
                    LedgerEntry::create([
                        'reference_type' => UserOffering::class,
                        'reference_id'   => $uo->id,
                        'account_code'   => $PAYABLE_PARTNER,
                        'entry_type'     => 'credit',
                        'amount'         => $partnerShare,
                        'description'    => 'مستحق لشريك عن الباقة',
                        'created_by'     => $createdByEmpId,
                        'is_locked'      => false,
                    ]);
                }
            }

            // 8) خدمات إضافية (اختياري)
            if (!empty($this->services) && class_exists(UserService::class)) {
                foreach ((array)$this->services as $serviceId) {
                    $user->userServices()->create([
                        'service_id' => $serviceId,
                        'status'     => 'applied',
                        'applied_at' => now(),
                    ]);
                }
            }

            // (اختياري) خطوة العائلة — متروكة حتى تزودني بجداول العائلة المطلوبة
        });

        Notification::make()->title('تم إنشاء المستخدم والانتسابات والباقات والقيود بنجاح')->success()->send();
        $this->redirect(static::getUrl());
    }
}
