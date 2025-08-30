@php
    $rows = \App\Models\UserService::with('service')->where('user_id',$user->id)->latest()->get();
@endphp
<table class="min-w-full text-sm">
    <thead>
    <tr class="text-gray-500">
        <th class="text-start p-2">الخدمة</th>
        <th class="text-start p-2">الحالة</th>
        <th class="text-start p-2">تاريخ الطلب</th>
    </tr>
    </thead>
    <tbody>
    @forelse($rows as $r)
        <tr class="border-t">
            <td class="p-2">{{ $r->service?->name ?? '—' }}</td>
            <td class="p-2">{{ $r->status }}</td>
            <td class="p-2">{{ optional($r->applied_at)->format('Y-m-d') }}</td>
        </tr>
    @empty
        <tr><td colspan="3" class="p-3 text-gray-500">لا توجد سجلات.</td></tr>
    @endforelse
    </tbody>
</table>
