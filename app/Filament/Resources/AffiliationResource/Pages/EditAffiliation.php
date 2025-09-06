<?php

namespace App\Filament\Resources\AffiliationResource\Pages;

use App\Filament\Resources\AffiliationResource;
use App\Models\UserOffering;
use App\Models\UserProfession;
use App\Models\PartnerOffering;
use App\Models\Profession;
use App\Models\Specialization;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EditAffiliation extends EditRecord{

    protected static string $resource = AffiliationResource::class;
    public function form(Form $form): Form{
        return $form->schema([
            Forms\Components\Section::make('بيانات العضو')->schema([
                Forms\Components\TextInput::make('user_name')->label('الاسم')->required(),
                Forms\Components\TextInput::make('user_email')->label('البريد الإلكتروني')->email()->required(),
                Forms\Components\TextInput::make('user_phone')->label('الهاتف'),
            ])->columns(3),

            Forms\Components\Section::make('العنوان')->schema([
                Forms\Components\TextInput::make('address_province')->label('المحافظة'),
                Forms\Components\TextInput::make('address_district')->label('القضاء'),
                Forms\Components\TextInput::make('address_subdistrict')->label('الناحية'),
                Forms\Components\TextInput::make('address_details')->label('تفاصيل العنوان')->columnSpanFull(),
            ])->columns(2),

            Forms\Components\Section::make('بيانات الانتساب')->schema([
                Forms\Components\TextInput::make('identity_number')->label('رقم الهوية'),
                Forms\Components\Select::make('status')->label('الحالة')->options([
                    'pending'   => 'معلّق',
                    'active'    => 'موافق عليه',
                    'rejected'  => 'مرفوض',
                    'suspended' => 'مجمّد',
                ])->required(),
                Forms\Components\DatePicker::make('joined_at')->label('تاريخ الانضمام')->native(false),
            ])->columns(3),

            Forms\Components\Section::make('المهنة / التخصص')->schema([
                Forms\Components\Select::make('profession_id')
                    ->label('المهنة')
                    ->options(fn () => Profession::orderBy('name')->pluck('name', 'id')->toArray())
                    ->searchable()->preload(),
                Forms\Components\Select::make('specialization_id')
                    ->label('التخصص')
                    ->options(fn (Forms\Get $get) => $get('profession_id')
                        ? Specialization::where('profession_id', $get('profession_id'))
                            ->orderBy('name')->pluck('name','id')->toArray()
                        : []
                    )
                    ->searchable()->preload(),
            ])->columns(2),

            Forms\Components\Section::make('باقات التأمين المرتبطة')->schema([
                Forms\Components\Repeater::make('offerings')
                    ->label('الباقات')
                    ->minItems(0)
                    ->reorderable(false)
                    ->schema([
                        Forms\Components\Hidden::make('id'),
                        Forms\Components\Select::make('partner_offering_id')
                            ->label('العرض (شركة/باقة/سعر)')
                            ->searchable()
                            ->preload()
                            ->options(fn () =>
                            PartnerOffering::with(['organization:id,name', 'package:id,name'])
                                ->orderBy('id')
                                ->get()
                                ->mapWithKeys(function ($po) {
                                    $label = "{$po->organization->name} — {$po->package->name} — ".number_format((float)$po->price)." IQD";
                                    return [$po->id => $label];
                                })->toArray()
                            )
                            ->required(),
                        Forms\Components\TextInput::make('platform_generated_number')->label('رقم المنصّة'),
                        Forms\Components\TextInput::make('partner_filled_number')->label('رقم الشريك'),
                        Forms\Components\Select::make('status')->label('الحالة')->options([
                            'applied'  => 'مقدّم',
                            'pending'  => 'معلّق',
                            'active'   => 'مفعّل',
                            'rejected' => 'مرفوض',
                        ]),
                        Forms\Components\DatePicker::make('applied_at')->label('تاريخ التقديم')->native(false),
                        Forms\Components\DatePicker::make('activated_at')->label('تاريخ التفعيل')->native(false),
                    ])->columns(3),
            ])->columnSpanFull(),
        ]);
    }
    /** تعبئة الحقول من العلاقات قبل عرض الفورم */
    protected function mutateFormDataBeforeFill(array $data): array{
        $record  = $this->record->loadMissing([
            'user.userProfiles',
            'userOfferings.partnerOffering.organization',
            'userOfferings.partnerOffering.package',
        ]);

        // العضو + الملف الشخصي
        $data['user_name']  = $record->user?->name;
        $data['user_email'] = $record->user?->email;
        $prof               = $record->user?->userProfiles?->first();

        $data['user_phone']         = $prof?->phone;
        $data['address_province']   = $prof?->address_province;
        $data['address_district']   = $prof?->address_district;
        $data['address_subdistrict']= $prof?->address_subdistrict;
        $data['address_details']    = $prof?->address_details;

        // المهنة/التخصص
        $up = UserProfession::where('user_affiliation_id', $record->id)->first();
        $data['profession_id']     = $up?->profession_id;
        $data['specialization_id'] = $up?->specialization_id;

        // العروض
        $data['offerings'] = $record->userOfferings->map(function (UserOffering $uo) {
            return [
                'id'                        => $uo->id,
                'partner_offering_id'       => $uo->partner_offering_id,
                'platform_generated_number' => $uo->platform_generated_number,
                'partner_filled_number'     => $uo->partner_filled_number,
                'status'                    => $uo->status,
                'applied_at'                => optional($uo->applied_at)?->format('Y-m-d'),
                'activated_at'              => optional($uo->activated_at)?->format('Y-m-d'),
            ];
        })->toArray();

        return $data;
    }
    /** حفظ جميع التغييرات في معاملة واحدة */
    protected function handleRecordUpdate(Model $record, array $data): Model{
        DB::transaction(function () use ($record, $data) {
            // 1) انتساب
            $record->update([
                'identity_number' => $data['identity_number'] ?? null,
                'status'          => $data['status'] ?? $record->status,
                'joined_at'       => $data['joined_at'] ?? $record->joined_at,
            ]);

            // 2) المستخدم
            if ($record->user) {
                $record->user->update([
                    'name'  => $data['user_name']  ?? $record->user->name,
                    'email' => $data['user_email'] ?? $record->user->email,
                ]);

                // 3) الملف الشخصي (أول Profile)
                $profile = $record->user->userProfiles()->first();
                if ($profile) {
                    $profile->update([
                        'phone'               => $data['user_phone'] ?? $profile->phone,
                        'address_province'    => $data['address_province'] ?? $profile->address_province,
                        'address_district'    => $data['address_district'] ?? $profile->address_district,
                        'address_subdistrict' => $data['address_subdistrict'] ?? $profile->address_subdistrict,
                        'address_details'     => $data['address_details'] ?? $profile->address_details,
                    ]);
                }
            }

            // 4) المهنة/التخصص
            if (! empty($data['profession_id']) || ! empty($data['specialization_id'])) {
                UserProfession::updateOrCreate(
                    ['user_affiliation_id' => $record->id],
                    [
                        'user_id'          => $record->user_id,
                        'profession_id'    => $data['profession_id']     ?? null,
                        'specialization_id'=> $data['specialization_id'] ?? null,
                    ]
                );
            }

            // 5) الباقات (إضافة/تحديث/حذف)
            $keepIds = [];
            foreach (($data['offerings'] ?? []) as $row) {
                $uo = null;
                if (!empty($row['id'])) {

                    $uo = UserOffering::where('id', $row['id'])
                        ->first();
                }
                if (! $uo) {
                    $uo = new UserOffering([
                        'user_id'             => $record->user_id,
                    ]);
                }

                $uo->partner_offering_id       = (int) ($row['partner_offering_id'] ?? $uo->partner_offering_id);
                $uo->platform_generated_number = $row['platform_generated_number'] ?? null;
                $uo->partner_filled_number     = $row['partner_filled_number']   ?? null;
                $uo->status                    = $row['status'] ?? $uo->status;
                $uo->applied_at                = !empty($row['applied_at'])   ? $row['applied_at']   : null;
                $uo->activated_at              = !empty($row['activated_at']) ? $row['activated_at'] : null;
                $uo->save();

                $keepIds[] = $uo->id;
            }

            // احذف التي أزيلت من الفورم
            UserOffering::where('user_id', $record->user->id)
                ->when(!empty($keepIds), fn($q) => $q->whereNotIn('id', $keepIds))
                ->when(empty($keepIds), fn($q) => $q) // لا شرط إضافي
                ->delete();
        });

        return $record->fresh();
    }

}
