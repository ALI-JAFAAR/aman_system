<?php
// app/Filament/Resources/ClaimResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\ClaimResource\Pages;
use App\Models\Claim;
use App\Models\User;
use App\Models\UserOffering;
use App\Models\Organization;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class ClaimResource extends Resource
{
    protected static ?string $model = Claim::class;

    protected static ?string $navigationGroup = 'المطالبات';
    protected static ?string $navigationLabel = 'المطالبات';
    protected static ?int    $navigationSort  = 10;
    protected static ?string $navigationIcon  = 'heroicon-o-clipboard-document-check';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\Section::make('بيانات المطالبة')->schema([

                // 1) المنتسبون فقط (أصحاب دور "منتسب")
                Forms\Components\Select::make('member_id')
                    ->label('المنتسب')
                    ->options(fn () =>
                    User::query()
                        ->whereHas('roles', fn (Builder $q) => $q->where('name', 'منتسب'))
                        ->orderBy('name')
                        ->pluck('name','id')
                        ->toArray()
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()
                    ->dehydrated(false), // لا نحفظه في قاعدة البيانات

                // 2) عروض/باقات المنتسب المحدد -> نحفظ user_offering_id
                Forms\Components\Select::make('user_offering_id')
                    ->label('الباقة/العرض المرتبط')
                    ->options(function (Get $get) {
                        $uid = (int) ($get('member_id') ?? 0);
                        if (! $uid) return [];

                        return UserOffering::query()
                            ->with(['partnerOffering.organization','partnerOffering.package'])
                            ->where('user_id', $uid)
                            ->orderByDesc('id')
                            ->get()
                            ->mapWithKeys(function ($uo) {
                                $org = $uo->partnerOffering?->organization?->name ?? '—';
                                $pkg = $uo->partnerOffering?->package?->name ?? '—';
                                $num = $uo->platform_generated_number ?: $uo->id;
                                return [
                                    $uo->id => "{$org} • {$pkg} — #{$num}",
                                ];
                            })
                            ->toArray();
                    })
                    ->required()
                    ->searchable()
                    ->preload()
                    ->helperText('اختر العرض/الباقة التي تتعلق بها المطالبة.'),

                Forms\Components\Select::make('type')
                    ->label('نوع المطالبة')
                    ->options([
                        'health' => 'صحية',
                        'legal'  => 'قانونية',
                        'financial'   => 'مصرفية',
                    ])
                    ->required(),

                Forms\Components\TextInput::make('title')
                    ->label('عنوان المطالبة')
                    ->maxLength(255),

                Forms\Components\Textarea::make('details')
                    ->label('تفاصيل')
                    ->rows(3),

                Forms\Components\DatePicker::make('accident_date')
                    ->label('تاريخ الحادثة/الحالة')
                    ->native(false),

                Forms\Components\TextInput::make('amount_requested')
                    ->label('المبلغ المطلوب')
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->required(),

                Forms\Components\FileUpload::make('attachments')
                    ->label('مرفقات')
                    ->directory('claims')
                    ->multiple()
                    ->downloadable()
                    ->openable()
                    ->imagePreviewHeight('80')
                    ->columnSpanFull(),

            ])->columns(2),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('id')->label('#')->sortable(),
            Tables\Columns\TextColumn::make('userOffering.user.name')->label('المنتسب')->searchable(),
            Tables\Columns\TextColumn::make('userOffering.partnerOffering.organization.name')->label('الجهة/الشركة')->toggleable(),
            Tables\Columns\TextColumn::make('userOffering.partnerOffering.package.name')->label('الباقة')->toggleable(),
            Tables\Columns\TextColumn::make('type')->label('النوع')->badge()
                ->colors([
                    'primary' => 'health',
                    'warning' => 'legal',
                    'info'    => 'bank',
                    'gray'    => 'other',
                ]),
            Tables\Columns\TextColumn::make('amount_requested')->label('المطلوب')
                ->formatStateUsing(fn ($state) => number_format((float) ($state ?? 0)) . ' IQD'),
            Tables\Columns\TextColumn::make('status')->label('الحالة')->badge()
                ->colors([
                    'gray'    => 'submitted',
                    'warning' => 'in_review',
                    'success' => 'approved',
                    'danger'  => 'rejected',
                    'primary' => 'paid',
                ]),
            Tables\Columns\TextColumn::make('created_at')->label('أُنشئت')->dateTime('Y-m-d H:i'),
        ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')->label('النوع')
                    ->options(['health'=>'صحية','legal'=>'قانونية','bank'=>'مصرفية','other'=>'أخرى']),
                Tables\Filters\SelectFilter::make('status')->label('الحالة')
                    ->options([
                        'submitted'=>'مقدّمة','in_review'=>'قيد المراجعة',
                        'approved'=>'مقبولة','rejected'=>'مرفوضة','paid'=>'مصروفة',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                // (إجراءات الاعتماد/الرفض/الصرف أضفناها في الرد السابق — تبقى كما هي)
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListClaims::route('/'),
            'create' => Pages\CreateClaim::route('/create'),
            'view'   => Pages\ViewClaim::route('/{record}'),
            'edit'   => Pages\EditClaim::route('/{record}/edit'),
        ];
    }
}

