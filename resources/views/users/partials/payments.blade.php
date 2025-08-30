@php
    $rows = $user->payments()->latest()->get();
@endphp
<table class="min-w-full text-sm">
    <thead>
    <tr class="text-gray-500">
        <th class="text-start p-2">الطريقة</th>
        <th class="text-start p-2">المبلغ</th>
        <th class="text-start p-2">التاريخ</th>
    </tr>
    </thead>
    <tbody>
    @forelse($rows as $p)
        <tr class="border-t">
            <td class="p-2">{{ $p->method }}</td>
            <td class="p-2">{{ number_format((float)$p->amount) }} IQD</td>
            <td class="p-2">{{ $p->created_at?->format('Y-m-d H:i') }}</td>
        </tr>
    @empty
        <tr><td colspan="3" class="p-3 text-gray-500">لا توجد مدفوعات.</td></tr>
    @endforelse
    </tbody>
</table>
