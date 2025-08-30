@php
    use App\Models\UserAffiliation;
    use App\Models\Organization;

    $rows = UserAffiliation::where('user_id',$user->id)->latest()->get();
@endphp

<table class="min-w-full text-sm">
    <thead>
    <tr class="text-gray-500">
        <th class="text-start p-2">الجهة</th>
        <th class="text-start p-2">رقم الهوية</th>
        <th class="text-start p-2">الحالة</th>
        <th class="text-start p-2">تاريخ الانضمام</th>
    </tr>
    </thead>
    <tbody>
    @forelse($rows as $r)
        <tr class="border-t">
            <td class="p-2">{{ \App\Models\Organization::where('id',$r->organization_id)->value('name') ?? '—' }}</td>
            <td class="p-2 font-mono">{{ $r->identity_number ?? '—' }}</td>
            <td class="p-2">{{ $r->status }}</td>
            <td class="p-2">{{ optional($r->joined_at)->format('Y-m-d') }}</td>
        </tr>
    @empty
        <tr><td colspan="4" class="p-3 text-gray-500">لا توجد سجلات.</td></tr>
    @endforelse
    </tbody>
</table>
