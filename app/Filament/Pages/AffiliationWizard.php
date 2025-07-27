<?php

namespace App\Filament\Pages;

use App\Models\Organization;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Hash;

class AffiliationWizard extends Page implements HasForms
{
    use InteractsWithForms;

    // خصائص Livewire لحفظ الحقول
    public string  $email           = '';
    public string  $password        = '';
    public string  $name            = '';
    public ?string $mother_name     = null;
    public ?string $national_id     = null;
    public ?string $date_of_birth   = null;
    public ?string $place_of_birth  = null;
    public string  $phone           = '';
    public ?int    $organization_id = null;
    public string  $user_type       = '';

    protected static string $view            = 'filament.pages.affiliation-wizard';
    protected static ?string $slug = 'affiliation';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel= 'تسجيل انتساب جديد';
    protected static ?int    $navigationSort = 1;

    protected function getFormSchema(): array
    {
        return [
            Wizard::make([
                Step::make('البيانات الأساسية')
                    ->schema([
                        TextInput::make('email')
                            ->label('البريد الإلكتروني')
                            ->email()
                            ->required()
                            ->unique(table: User::class, column: 'email'),
                        TextInput::make('password')
                            ->label('كلمة المرور')
                            ->password()
                            ->required()
                            ->minLength(8)
                            ->dehydrateStateUsing(fn($s) => Hash::make($s)),
                        TextInput::make('name')->label('الاسم الرباعي')->required(),
                        TextInput::make('mother_name')->label('اسم الأم'),
                        TextInput::make('national_id')->label('رقم الهوية'),
                        DatePicker::make('date_of_birth')->label('تاريخ الميلاد'),
                        TextInput::make('place_of_birth')->label('مكان الميلاد'),
                        TextInput::make('phone')->label('رقم الهاتف')->required(),
                    ])
                    ->columns(2),

                Step::make('الانتساب')
                    ->schema([
                        Select::make('organization_id')
                            ->label('اختر الجهة')
                            ->options(Organization::pluck('name','id')->toArray())
                            ->required(),
                        Select::make('user_type')
                            ->label('نوع المستخدم')
                            ->options([
                                'worker'   => 'عامل نقابة',
                                'employee' => 'موظف قطاع عام',
                                'owner'    => 'رب عمل',
                            ])
                            ->required(),
                    ])
                    ->columns(2),
            ]),
        ];
    }



    public function submit(): void
    {
        $user = User::create([
            'name'     => $this->name,
            'email'    => $this->email,
            'password' => $this->password,
        ]);

        $user->userProfiles()->create([
            'name'           => $this->name,
            'mother_name'    => $this->mother_name,
            'national_id'    => $this->national_id,
            'date_of_birth'  => $this->date_of_birth,
            'place_of_birth' => $this->place_of_birth,
            'phone'          => $this->phone,
        ]);

        $user->affiliations()->create([
            'organization_id' => $this->organization_id,
            'status'          => 'pending',
            'joined_at'       => now(),
            'user_type'       => $this->user_type,
        ]);

        $this->notify('success', 'تم إنشاء حسابك وطلب الانتساب بنجاح!');
        $this->redirect(route('filament.pages.affiliation'));
    }
}
