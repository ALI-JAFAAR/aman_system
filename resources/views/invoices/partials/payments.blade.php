@php
    use App\Models\Payment;

    /** @var \App\Models\Invoice $invoice */
    $rows = Payment::where('invoice_id', $invoice->id)->orderBy('id')->get();
@endphp

@if ($rows->isEmpty())
    <div class="text-gray-500">لا توجد مدفوعات.</div>
@else
    <table class="w-full text-sm">
        <thead class="border-b font-semibold">
        <tr class="text-right">
            <th class="py-2">الطريقة</th>
            <th class="py-2">المبلغ</th>
            <th class="py-2">التاريخ</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($rows as $p)
            <tr class="border-b last:border-0">
                <td class="py-2">{{ $p->method }}</td>
                <td class="py-2">{{ number_format((float)$p->amount) }} IQD</td>
                <td class="py-2">{{ optional($p->created_at)->format('Y-m-d H:i') }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif
