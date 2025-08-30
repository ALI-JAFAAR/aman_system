@php
    use App\Models\InvoiceItem;
    use App\Models\UserOffering;
    use App\Models\PartnerOffering;
    use App\Models\Organization;

    /** @var \App\Models\Invoice $invoice */
    $items = InvoiceItem::where('invoice_id', $invoice->id)
        ->where('item_type', 'offering')
        ->get();
@endphp

@if ($items->isEmpty())
    <div class="text-gray-500">لا توجد بنود.</div>
@else
    <table class="w-full text-sm">
        <thead class="border-b font-semibold">
        <tr class="text-right">
            <th class="py-2">الوصف</th>
            <th class="py-2">السعر</th>
            <th class="py-2">الشريك / الباقة</th>
            <th class="py-2">الجهة المستضيفة</th>
            <th class="py-2">حصة الشريك</th>
            <th class="py-2">حصة الجهة</th>
            <th class="py-2">حصة المنصّة</th>
            <th class="py-2">التوزيع</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($items as $it)
            @php
                $uo = $it->reference_id ? UserOffering::find($it->reference_id) : null;
                $po = $uo ? PartnerOffering::with(['organization','package'])->find($uo->partner_offering_id) : null;
                $partnerAndPackage = $po
                    ? ($po->organization?->name ?? '—') . ' — ' . ($po->package?->name ?? ('#'.$po->id))
                    : '—';
                $hostName = $it->organization_id
                    ? Organization::where('id', $it->organization_id)->value('name')
                    : '—';
                $dist = $it->distribution_snapshot ?? null; // expects array (cast on model)
            @endphp
            <tr class="border-b last:border-0 align-top">
                <td class="py-2">{{ $it->description }}</td>
                <td class="py-2">{{ number_format((float)$it->line_total) }} IQD</td>
                <td class="py-2">{{ $partnerAndPackage }}</td>
                <td class="py-2">{{ $hostName ?? '—' }}</td>
                <td class="py-2">{{ number_format((float)($it->partner_share ?? 0)) }} IQD</td>
                <td class="py-2">{{ number_format((float)($it->host_share ?? 0)) }} IQD</td>
                <td class="py-2">{{ number_format((float)($it->platform_share ?? 0)) }} IQD</td>
                <td class="py-2">
                    @if (is_array($dist))
                        <div class="space-y-1">
                            <div>شريك %: {{ $dist['partner_percent'] ?? '—' }}</div>
                            <div>منصّة %: {{ $dist['platform_percent'] ?? '—' }}</div>
                            <div>جهة (أساسي) %: {{ $dist['host_org_percent'] ?? '—' }}</div>
                            <div>جهة (مفعّل) %: {{ $dist['enabled_host_pct'] ?? '—' }}</div>
                        </div>
                    @else
                        —
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif
