<?php

namespace App\Filament\Resources\AffiliationResource\Pages;

use App\Filament\Resources\AffiliationResource;
use App\Models\UserOffering;
use App\Models\UserProfession;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

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

            Section::make('باقات التأمين الخاصة بالعضو')->schema([
                \Filament\Infolists\Components\RepeatableEntry::make('offerings_snapshot')
                    ->label('') // heading line kept empty
                    ->state(function ($record) {
                        // Pull the member’s offerings with the bits we actually render
                        $items = UserOffering::query()
                            ->where('user_id', $record->user_id)
                            ->with([
                                'partnerOffering:id,organization_id,package_id,price',
                                'partnerOffering.organization:id,name',
                                'partnerOffering.package:id,name',
                            ])
                            ->orderByDesc('id')
                            ->limit(10)
                            ->get();

                        if ($items->isEmpty()) {
                            return []; // RepeatableEntry will render nothing (clean)
                        }

                        return $items->map(function ($uo) {
                            $activated = $uo->activated_at ?? $uo->approved_at; // handle both columns
                            return [
                                'partner'   => $uo->partnerOffering?->organization?->name,
                                'package'   => $uo->partnerOffering?->package?->name,
                                'status'    => $uo->status,
                                'price'     => (float) ($uo->partnerOffering?->price ?? 0),
                                'platform'  => $uo->platform_generated_number,
                                'partnerNo' => $uo->partner_filled_number,
                                'applied'   => optional($uo->applied_at)->format('Y-m-d'),
                                'activated' => optional($activated)->format('Y-m-d'),
                            ];
                        })->all();
                    })
                    ->schema([
                        \Filament\Infolists\Components\Grid::make(3)->schema([
                            \Filament\Infolists\Components\TextEntry::make('partner')->label('شركة التأمين')->placeholder('—'),
                            \Filament\Infolists\Components\TextEntry::make('package')->label('الباقة')->placeholder('—'),
                            \Filament\Infolists\Components\TextEntry::make('status')->label('الحالة')->badge()
                                ->colors(['warning' => 'applied', 'success' => 'active', 'danger' => 'rejected']),
                            \Filament\Infolists\Components\TextEntry::make('price')->label('السعر')
                                ->formatStateUsing(fn ($state) => number_format((float) $state) . ' IQD'),
                            \Filament\Infolists\Components\TextEntry::make('platform')->label('رقم المنصّة')->placeholder('—'),
                            \Filament\Infolists\Components\TextEntry::make('partnerNo')->label('رقم الشريك')->placeholder('—'),
                            \Filament\Infolists\Components\TextEntry::make('applied')->label('تقديم')->placeholder('—'),
                            \Filament\Infolists\Components\TextEntry::make('activated')->label('تفعيل')->placeholder('—'),
                        ]),
                    ])
                    // If no items, hide the whole block to avoid an empty box
                    ->visible(fn ($state) => is_array($state) && count($state) > 0),
            ])
                ->collapsible(),

        ]);
    }
}
