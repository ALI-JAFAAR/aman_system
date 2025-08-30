<?php
namespace App\Filament\Resources;

use App\Filament\Resources\ReconciliationResource\Pages;
use App\Models\Contract;
use App\Models\Reconciliation;
use App\Models\Organization;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;

class ReconciliationResource extends Resource{
    protected static ?string $model = Reconciliation::class;
    protected static ?string $navigationGroup = 'المحاسبة والتسويات';
    protected static ?string $navigationLabel = 'التسويات';
    protected static ?int    $navigationSort  = 10;
    protected static ?string $navigationIcon  = 'heroicon-o-arrows-right-left';


    protected static ?string $pluralLabel     = 'التسويات';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\Section::make('بيانات التسوية')
                ->schema([
                    Forms\Components\Radio::make('kind')
                        ->label('نوع التسوية')
                        ->options(['partner' => 'الشريك (2100)', 'host' => 'الجهة المضيفة (2200)'])
                        ->default('partner')
                        ->required()
                        ->reactive(),

                    Forms\Components\Select::make('organization_id')
                        ->label('الجهة')
                        ->options(Organization::orderBy('name')->pluck('name','id')->toArray())
                        ->required()
                        ->searchable()
                        ->preload()
                        ->live()
                        ->afterStateUpdated(function (Set $set, ?string $state) {
                            if (! $state) {
                                $set('period_start', null);
                                $set('period_end', null);
                                return;
                            }

                            $c = Contract::activeForOrganization((int)$state);
                            if ($c) {
                                $start = optional($c->contract_start)?->format('Y-m-d') ?? now()->toDateString();
                                // نهاية الفترة = اليوم أو قبل نهاية العقد بيوم (لأن contract_end > اليوم)
                                $end   = min(
                                    now()->toDateString(),
                                    optional($c->contract_end)?->format('Y-m-d') ?? now()->toDateString()
                                );

                                $set('period_start', $start);
                                $set('period_end', $end);
                            } else {
                                $set('period_start', null);
                                $set('period_end', null);
                                Notification::make()
                                    ->title('لا يوجد عقد فعّال لهذه الجهة')
                                    ->warning()
                                    ->send();
                            }
                        })
                        ->helperText('سيتم اختيار العقد الفعّال تلقائيًا بناءً على تاريخ اليوم.'),

                    Forms\Components\DatePicker::make('period_start')->label('بداية الفترة')->required(),
                    Forms\Components\DatePicker::make('period_end')->label('نهاية الفترة')->required(),

                ])->columns(2),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table{
        return $table->columns([
            Tables\Columns\TextColumn::make('id')->label('#')->sortable(),
            Tables\Columns\TextColumn::make('organization.name')->label('الجهة')->searchable(),
            Tables\Columns\TextColumn::make('period_start')->label('من')->date(),
            Tables\Columns\TextColumn::make('period_end')->label('إلى')->date(),
            Tables\Columns\TextColumn::make('total_gross_amount')->label('إجمالي المبيعات')->formatStateUsing(fn ($state) => number_format((float) $state).' IQD'),
            Tables\Columns\TextColumn::make('total_partner_share')->label('حصة الشريك')->formatStateUsing(fn ($state) => number_format((float) $state).' IQD'),
            Tables\Columns\TextColumn::make('total_organization_share')->label('حصة الجهة')->formatStateUsing(fn ($state) => number_format((float) $state).' IQD'),
            Tables\Columns\TextColumn::make('total_platform_share')->label('حصة المنصّة')->formatStateUsing(fn ($state) => number_format((float) $state).' IQD'),
            Tables\Columns\TextColumn::make('status')->badge()
                ->colors([
                    'warning' => 'draft',
                    'info'    => 'platform_reconciled',
                    'success' => 'partner_reconciled',
                    'gray'    => 'closed',
                ]),
            Tables\Columns\TextColumn::make('platform_reconciled_at')->label('تاريخ منصّة')->dateTime()->toggleable(),
        ])->filters([
            Tables\Filters\SelectFilter::make('status')->label('الحالة')->options([
                'draft'=>'مسودة','platform_reconciled'=>'مصالحة المنصّة',
                'partner_reconciled'=>'مصالحة الشريك','closed'=>'مقفلة'
            ]),
        ])->actions([
            Tables\Actions\ViewAction::make(),
            Tables\Actions\Action::make('platformOk')->label('تأكيد المنصّة')
                ->visible(fn($record)=>$record->status==='draft')
                ->action(function (Reconciliation $record) {
                    app(\App\Services\ReconciliationBuilder::class)
                        ->markPlatformReconciled($record, optional(auth()->user()?->employee)->id ?? null);
                }),
            Tables\Actions\Action::make('partnerOk')->label('اعتماد الشريك')
                ->visible(fn($record)=>in_array($record->status,['draft','platform_reconciled']))
                ->action(function (Reconciliation $record) {
                    app(\App\Services\ReconciliationBuilder::class)
                        ->markPartnerReconciled($record, optional(auth()->user()?->employee)->id ?? null);
                }),
            Tables\Actions\Action::make('close')->label('إقفال')
                ->color('gray')
                ->requiresConfirmation()
                ->visible(fn($record)=>in_array($record->status,['platform_reconciled','partner_reconciled']))
                ->action(function (Reconciliation $record) {
                    app(\App\Services\ReconciliationBuilder::class)->close($record, true);
                }),
        ])->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ReconciliationResource\Pages\ListReconciliations::route('/'),
            'create' => ReconciliationResource\Pages\CreateReconciliation::route('/create'),
            'view'   => ReconciliationResource\Pages\ViewReconciliation::route('/{record}'),
        ];
    }
}
