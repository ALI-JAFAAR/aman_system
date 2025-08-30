@php
    use App\Models\InvoiceItem;

    /** @var \App\Models\Invoice $invoice */
    $items = InvoiceItem::where('invoice_id', $invoice->id)
        ->where('item_type', 'service')
        ->get();
@endphp

@if ($items->isEmpty())
    <div class="text-gray-500">لا توجد خدمات.</div>
@else
    <table class="w-full text-sm">
        <thead class="border-b font-semibold">
        <tr class="text-right">
            <th class="py-2">الخدمة</th>
            <th class="py-2">المبلغ</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($items as $it)
            <tr class="border-b last:border-0">
                <td class="py-2">{{ $it->description }}</td>
                <td class="py-2">{{ number_format((float)$it->line_total) }} IQD</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif
