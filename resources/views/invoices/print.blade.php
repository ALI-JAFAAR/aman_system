<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>فاتورة {{ $invoice->number }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body{font-family:ui-sans-serif,system-ui,-apple-system,"Segoe UI",Roboto,"Helvetica Neue","Noto Sans",Arial,"Apple Color Emoji","Segoe UI Emoji";color:#111827;margin:24px}
        .container{max-width:900px;margin:0 auto}
        .head{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px}
        .muted{color:#6b7280}
        table{width:100%;border-collapse:collapse;margin-top:12px}
        th,td{border:1px solid #e5e7eb;padding:8px;text-align:right}
        th{background:#f9fafb}
        .totals{margin-top:16px;display:flex;gap:16px;justify-content:flex-end}
        .btn{padding:8px 12px;border:1px solid #e5e7eb;background:#111827;color:#fff;border-radius:6px;text-decoration:none}
        @media print {.no-print{display:none}}
    </style>
</head>
<body>
<div class="container">
    <div class="head">
        <div>
            <h2 style="margin:0">فاتورة: {{ $invoice->number }}</h2>
            <div class="muted">تاريخ الإصدار: {{ optional($invoice->issued_at)->format('Y-m-d H:i') }}</div>
            <div class="muted">الحالة: {{ $invoice->status }}</div>
        </div>
        <button class="btn no-print" onclick="window.print()">طباعة</button>
    </div>

    <hr>

    <h3>العميل</h3>
    <div>{{ $user->name ?? '—' }} — {{ $user->email ?? '—' }}</div>
    @if($profile)
        <div class="muted">
            {{ $profile->phone ?? '' }}<br>
            {{ $profile->address_details }} {{ $profile->address_subdistrict ? ' - '.$profile->address_subdistrict : '' }}
            {{ $profile->address_district ? ' - '.$profile->address_district : '' }}
            {{ $profile->address_province ? ' - '.$profile->address_province : '' }}
        </div>
    @endif

    <h3>بنود الفاتورة</h3>
    <table>
        <thead>
        <tr>
            <th>الوصف</th>
            <th>النوع</th>
            <th>الكمية</th>
            <th>السعر</th>
            <th>الإجمالي</th>
        </tr>
        </thead>
        <tbody>
        @foreach($items as $it)
            <tr>
                <td>{{ $it->description }}</td>
                <td>
                    @php
                        $label = ['affiliation_fee'=>'رسوم انتساب','offering'=>'باقة','service'=>'خدمة'][$it->item_type] ?? $it->item_type;
                    @endphp
                    {{ $label }}
                </td>
                <td>{{ (int)($it->qty ?? 1) }}</td>
                <td>{{ number_format((float)$it->unit_price) }} IQD</td>
                <td>{{ number_format((float)$it->line_total) }} IQD</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div>
            <div>قبل الخصم: <strong>{{ number_format((float)$invoice->subtotal) }} IQD</strong></div>
            <div>الخصم: <strong>{{ number_format((float)$invoice->discount_amount) }} IQD</strong></div>
            <div>الإجمالي: <strong>{{ number_format((float)$invoice->total) }} IQD</strong></div>
            <div>مدفوع: <strong>{{ number_format((float)$invoice->paid) }} IQD</strong></div>
            <div>المتبقي: <strong>{{ number_format((float)$invoice->balance) }} IQD</strong></div>
        </div>
    </div>

    @if($payments->count())
        <h3>الدفعات</h3>
        <table>
            <thead>
            <tr>
                <th>التاريخ</th>
                <th>الطريقة</th>
                <th>المبلغ</th>
                <th>مرجع</th>
            </tr>
            </thead>
            <tbody>
            @foreach($payments as $p)
                <tr>
                    <td>{{ optional($p->created_at)->format('Y-m-d H:i') }}</td>
                    <td>{{ ['cash'=>'نقدًا','pos'=>'POS','zaincash'=>'زين كاش','bank'=>'تحويل بنكي'][$p->method] ?? $p->method }}</td>
                    <td>{{ number_format((float)$p->amount) }} IQD</td>
                    <td>{{ $p->reference ?? '—' }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
</div>
</body>
</html>
