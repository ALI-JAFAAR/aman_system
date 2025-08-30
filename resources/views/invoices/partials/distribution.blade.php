@php
    $snap = $getState();
    if (is_string($snap)) {
        $snap = json_decode($snap, true);
    }
@endphp
<div class="text-sm leading-6">
    @if (is_array($snap))
        <div>الشريك: <strong>{{ $snap['partner_percent'] ?? 0 }}%</strong></div>
        <div>المنصّة (إجمالي): <strong>{{ $snap['platform_percent'] ?? 0 }}%</strong></div>
        <div>نسبة الجهة (مخطط): <strong>{{ $snap['host_org_percent'] ?? 0 }}%</strong></div>
        <div>نسبة الجهة المفعلة: <strong>{{ $snap['enabled_host_pct'] ?? 0 }}%</strong></div>
    @else
        <span class="text-gray-500">لا توجد بيانات توزيع.</span>
    @endif
</div>
