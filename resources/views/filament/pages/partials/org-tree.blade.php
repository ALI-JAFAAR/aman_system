@php
    $typeColors = [
        'general_union' => 'bg-blue-100 text-blue-700',
        'sub_union'     => 'bg-indigo-100 text-indigo-700',
        'trade_union'   => 'bg-teal-100 text-teal-700',
        'government_institution' => 'bg-amber-100 text-amber-700',
        'insurance_company' => 'bg-rose-100 text-rose-700',
        'organization'  => 'bg-gray-100 text-gray-700',
        'guild'         => 'bg-emerald-100 text-emerald-700',
        'platform'      => 'bg-purple-100 text-purple-700',
        'law_firm'      => 'bg-slate-100 text-slate-700',
    ];
@endphp

<ul class="space-y-2">
    @foreach ($nodes as $n)
        <li>
            <div class="flex items-center gap-3">
                <span class="font-semibold">{{ $n['name'] }}</span>
                @if(!empty($n['code']))
                    <span class="px-2 py-0.5 text-xs rounded bg-gray-200">{{ $n['code'] }}</span>
                @endif
                <span class="px-2 py-0.5 text-xs rounded {{ $typeColors[$n['type']] ?? 'bg-gray-100 text-gray-700' }}">
                {{ $n['type'] }}
            </span>
            </div>

            @if (!empty($n['children']))
                <div class="ms-5 mt-2 border-s ps-4">
                    @include('filament.pages.partials.org-tree', ['nodes' => $n['children']])
                </div>
            @endif
        </li>
    @endforeach
</ul>
