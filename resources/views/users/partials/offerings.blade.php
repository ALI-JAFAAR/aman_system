@php
    use App\Models\UserOffering;
    use App\Models\PartnerOffering;

    $rows = UserOffering::with('partnerOffering.organization','partnerOffering.package')
        ->where('user_id',$user->id)->latest()->get();
@endphp

<table class="min-w-full text-sm">
    <thead>
    <tr class="text-gray-500">
        <th class="text-start p-2">الشريك</th>
        <th class="text-start p-2">الباقة</th>
        <th class="text-start p-2">السعر</th>
        <th class="text-start p-2">الحالة</th>
        <th class="text-start p-2">رقم التأمين</th>
    </tr>
    </thead>
    <tbody>
    @forelse($rows as $r)
        @php $po = $r->partnerOffering; @endphp
        <tr class="border-t">
            <td class="p-2">{{ $po?->organization?->name ?? '—' }}</td>
            <td class="p-2">{{ $po?->package?->name ?? '—' }}</td>
            <td class="p-2">{{ number_format((float)($po?->price ?? 0)) }} IQD</td>
            <td class="p-2">{{ $r->status }}</td>
            <td class="p-2 font-mono">
                {{ $r->partner_number ?? $r->platform_generated_number ?? '—' }}
            </td>
        </tr>
    @empty
        <tr><td colspan="5" class="p-3 text-gray-500">لا توجد سجلات.</td></tr>
    @endforelse
    </tbody>
</table>
