<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use App\Models\User;

class ViewUser extends ViewRecord{

    protected static string $resource = UserResource::class;
    public function infolist(Infolist $infolist): Infolist{
        /** @var User $record */
        $record = $this->getRecord();
        $record->loadMissing(['userProfiles', 'invoices']);

        return $infolist->schema([
            Section::make('البيانات الأساسية')
                ->schema([
                    TextEntry::make('name')->label('الاسم'),
                    TextEntry::make('email')->label('البريد'),
                    TextEntry::make('userProfiles.phone')->label('الهاتف')->placeholder('—'),
                    TextEntry::make('parent_id')->label('تابع لعائلة مستخدم؟')
                        ->formatStateUsing(fn($state) => $state ? 'نعم' : 'لا'),
                    TextEntry::make('family_relation')->label('صلة القرابة')->placeholder('—'),
                    TextEntry::make('created_at')->label('تاريخ الإنشاء')->dateTime('Y-m-d H:i'),
                ])->columns(3),

            Section::make('المحفظة والرصيد')
                ->schema([
                    ViewEntry::make('wallet_block')
                        ->view('users.partials.wallet')
                        ->viewData(['user' => $record])
                        ->columnSpanFull(),
                ]),

            Section::make('سجل الانتسابات')
                ->schema([
                    ViewEntry::make('affiliations_block')
                        ->view('users.partials.affiliations')
                        ->viewData(['user' => $record])
                        ->columnSpanFull(),
                ])->collapsible(),

            Section::make('الباقات / العروض')
                ->schema([
                    ViewEntry::make('offerings_block')
                        ->view('users.partials.offerings')
                        ->viewData(['user' => $record])
                        ->columnSpanFull(),
                ])->collapsible(),

            Section::make('الخدمات الإضافية')
                ->schema([
                    ViewEntry::make('services_block')
                        ->view('users.partials.services')
                        ->viewData(['user' => $record])
                        ->columnSpanFull(),
                ])->collapsible(),

            Section::make('العائلة والعاملون المرتبطون')
                ->schema([
                    ViewEntry::make('family_block')
                        ->view('users.partials.family')
                        ->viewData(['user' => $record])
                        ->columnSpanFull(),
                    ViewEntry::make('related_workers_block')
                        ->view('users.partials.related-workers')
                        ->viewData(['user' => $record])
                        ->columnSpanFull(),
                ])->collapsible(),

            Section::make('الفواتير والمدفوعات')
                ->schema([
                    ViewEntry::make('invoices_block')
                        ->view('users.partials.invoices')
                        ->viewData(['user' => $record])
                        ->columnSpanFull(),
                    ViewEntry::make('payments_block')
                        ->view('users.partials.payments')
                        ->viewData(['user' => $record])
                        ->columnSpanFull(),
                ])->collapsible(),

            Section::make('قيود اليومية')
                ->schema([
                    ViewEntry::make('ledgers_block')
                        ->view('users.partials.ledgers')
                        ->viewData(['user' => $record])
                        ->columnSpanFull(),
                ])->collapsible(),
        ]);
    }
}
