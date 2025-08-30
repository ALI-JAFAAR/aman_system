<?php

namespace App\Filament\Resources\AffiliationResource\Pages;

use App\Filament\Resources\AffiliationResource;
use App\Models\UserOffering;
use App\Models\UserProfession;
use Carbon\Carbon;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\DB;
use Schema;

class ViewAffiliation extends ViewRecord{
    protected static string $resource = AffiliationResource::class;
    private static function orgTypeLabel($state): string{
        // Accept enum or string
        $key = $state instanceof \BackedEnum ? $state->value : (string) $state;

        $map = [
            'guild'         => 'نقابة',
            'trade_union'   => 'اتحاد مهني',
            'sub_union'     => 'اتحاد فرعي',
            'general_union' => 'اتحاد عام',
            'organization'  => 'مؤسسة',
        ];

        return $map[$key] ?? $key;
    }
    public function infolist(Infolist $infolist): Infolist{
        return $infolist->schema([
            Section::make('بيانات الانتساب')->schema([
                Grid::make(2)->schema([
                    TextEntry::make('id')->label('#'),
                    TextEntry::make('identity_number')->label('رقم الهوية')->placeholder('—'),
                    TextEntry::make('status')->label('الحالة')->badge()
                        ->colors(['warning' => 'pending', 'success' => 'active', 'danger' => 'rejected']),
                    TextEntry::make('joined_at')->label('تاريخ الانضمام')->date(),
                ]),
            ])->collapsible(),

            Section::make('الجهة')->schema([
                Grid::make(2)->schema([
                    TextEntry::make('organization.name')->label('الاسم'),
                    TextEntry::make('organization.type')
                        ->label('النوع')
                        ->formatStateUsing(fn ($state) => self::orgTypeLabel($state)),
                    TextEntry::make('organization.code')->label('الكود')->placeholder('—'),
                    TextEntry::make('organization.organization.name')->label('تابعة لـ')->placeholder('—'),
                ]),
            ])->collapsible(),

            Section::make('العضو')->schema([
                Grid::make(2)->schema([
                    TextEntry::make('user.name')->label('الاسم'),
                    TextEntry::make('user.email')->label('البريد الإلكتروني'),
                    TextEntry::make('user.userProfiles.0.phone')->label('الهاتف')->placeholder('—'),
                    TextEntry::make('user.userProfiles.0.address_province')->label('المحافظة')->placeholder('—'),
                    TextEntry::make('user.userProfiles.0.address_district')->label('القضاء')->placeholder('—'),
                    TextEntry::make('user.userProfiles.0.address_subdistrict')->label('الناحية')->placeholder('—'),
                    TextEntry::make('user.userProfiles.0.address_details')->label('العنوان')->placeholder('—'),
                ]),
            ])->collapsible(),

            Section::make('المهنة / التخصص')->schema([
                Grid::make(2)->schema([
                    TextEntry::make('profession_name')
                        ->label('المهنة')
                        ->state(function ($record) {
                            $up = UserProfession::with('profession')->where('user_affiliation_id', $record->id)->first();
                            return $up?->profession?->name ?: '—';
                        }),
                    TextEntry::make('specialization_name')
                        ->label('التخصص')
                        ->state(function ($record) {
                            $up = UserProfession::with('specialization')->where('user_affiliation_id', $record->id)->first();
                            return $up?->specialization?->name ?: '—';
                        }),
                ]),
            ])->collapsible(),
            Section::make('باقات التأمين')->schema([
                ViewEntry::make('offeringsView')
                    ->view('affiliations.offerings')
                    ->viewData(fn ($record) => [
                        'affiliation' => $record,
                        'restrictToAffiliationOrg' => false,
                    ]),
            ])->collapsible()



        ]);
    }
}
