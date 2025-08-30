<?php
// app/Filament/Resources/ServiceResource.php
namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Get;
use Filament\Forms\Set;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;

class ServiceResource extends Resource{
    protected static ?string $model = Service::class;

    protected static ?string $navigationGroup = 'الإدارة';
    protected static ?string $navigationLabel = 'الخدمات';
    protected static ?int    $navigationSort  = 20;
    protected static ?string $navigationIcon  = 'heroicon-o-wrench-screwdriver';

    protected static ?string $modelLabel       = 'خدمة';
    protected static ?string $pluralModelLabel = 'الخدمات';

    public static function form(Form $form): Form{

        return $form->schema([
            Section::make('بيانات الخدمة')->schema([
                TextInput::make('name')
                    ->label('اسم الخدمة')
                    ->required()
                    ->maxLength(255),

                TextInput::make('code')
                    ->label('الكود/المعرّف (اختياري)')
                    ->helperText('مثال: car_id, house_insurance')
                    ->alphaDash()
                    ->unique(ignoreRecord: true)
                    ->maxLength(100),

                TextInput::make('base_price')
                    ->label('السعر الأساسي')
                    ->numeric()
                    ->prefix('IQD')
                    ->rules(['nullable','numeric','min:0']),

                Toggle::make('is_active')
                    ->label('مفعّلة؟')
                    ->default(true),

                Textarea::make('description')
                    ->label('وصف مختصر')
                    ->rows(3),
            ])->columns(2),

            Section::make('نموذج طلب الخدمة (يُعرض للعامل)')->schema([
                Repeater::make('request_schema')
                    ->label('حقول نموذج الطلب')
                    ->reorderableWithDragAndDrop()
                    ->collapsible()
                    ->defaultItems(0)
                    ->schema([
                        TextInput::make('key')
                            ->label('المفتاح (Key)')
                            ->helperText('أحرف وأرقام وشرطة سفلية فقط. مثال: car_plate, issue_date')
                            ->required()
                            ->regex('/^[A-Za-z][A-Za-z0-9_]*$/')
                            ->maxLength(64),

                        TextInput::make('label')
                            ->label('التسمية الظاهرة')
                            ->required()
                            ->maxLength(255),

                        Select::make('type')
                            ->label('النوع')
                            ->required()
                            ->options([
                                'text'       => 'نص',
                                'textarea'   => 'منطقة نص',
                                'number'     => 'رقم',
                                'date'       => 'تاريخ',
                                'time'       => 'وقت',
                                'select'     => 'قائمة اختيار',
                                'radio'      => 'اختيار واحد (راديو)',
                                'checkbox'   => 'صح/خطأ',
                                'file'       => 'ملف',
                                'image'      => 'صورة',
                            ])
                            ->live(),

                        KeyValue::make('options')
                            ->label('خيارات (لنمط select/radio)')
                            ->keyLabel('القيمة (value)')
                            ->valueLabel('النص الظاهر (label)')
                            ->addButtonLabel('إضافة خيار')
                            ->visible(fn (Get $get) => in_array($get('type'), ['select','radio']))
                            ->nullable(),

                        Toggle::make('required')
                            ->label('إجباري؟')
                            ->default(false),

                        TextInput::make('placeholder')
                            ->label('نص مساعد (Placeholder)')
                            ->maxLength(255)
                            ->visible(fn (Get $get) => in_array($get('type'), ['text','textarea','number','date','time','select'])),

                        TextInput::make('default')
                            ->label('قيمة افتراضية')
                            ->maxLength(255)
                            ->visible(fn (Get $get) => in_array($get('type'), ['text','textarea','number','date','time','select','radio'])),

                        TextInput::make('rules')
                            ->label('قواعد التحقق (Laravel)')
                            ->helperText('مثال: numeric|min:1|max:10 أو date')
                            ->maxLength(255),

                    ])->columns(2)
                    ->helperText('رتّب حقول الطلب كما تريد أن تظهر للعامل.'),
            ])->columns(1),
        ]);
    }

    public static function table(Table $table): Table{
        return $table->columns([
            TextColumn::make('id')->label('#')->sortable(),
            TextColumn::make('name')->label('الخدمة')->searchable()->sortable(),
            TextColumn::make('code')->label('الكود')->toggleable(),
            TextColumn::make('base_price')->label('السعر الأساسي')->money('iqd', true)->sortable(),
            ToggleColumn::make('is_active')->label('مفعّلة'),
        ])
            ->defaultSort('id', 'desc')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array{
        return [
            'index'  => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit'   => Pages\EditService::route('/{record}/edit'),
        ];
    }
}
