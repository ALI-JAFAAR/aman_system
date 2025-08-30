<x-filament::page>
    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3">
        <div>
            <label class="text-sm text-gray-600">الجهة</label>
            <select name="orgId" class="w-full rounded-lg border-gray-300">
                <option value="">— اختر —</option>
                @foreach ($this->organizations as $org)
                    <option value="{{ $org->id }}" @selected(request('orgId') == $org->id)>{{ $org->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-sm text-gray-600">من تاريخ</label>
            <input type="date" name="from" value="{{ $this->from }}" class="w-full rounded-lg border-gray-300">
        </div>
        <div>
            <label class="text-sm text-gray-600">إلى تاريخ</label>
            <input type="date" name="to" value="{{ $this->to }}" class="w-full rounded-lg border-gray-300">
        </div>
        <div class="flex items-end">
            <button class="px-4 py-2 rounded-lg bg-primary-600 text-white">عرض</button>
        </div>
    </form>

    @if ($this->orgId)
        <div class="mt-6 rounded-xl border">
            <div class="px-4 py-3 bg-gray-50 dark:bg-gray-800 flex items-center justify-between">
                <div>عدد الانتسابات: <b>{{ $this->totals['count'] }}</b></div>
                <div>إجمالي الرسوم: <b>{{ $this->totals['sum'] }}</b></div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="p-2 text-right">#</th>
                        <th class="p-2 text-right">رقم الهوية</th>
                        <th class="p-2 text-right">المنتسب</th>
                        <th class="p-2 text-right">البريد</th>
                        <th class="p-2 text-right">تاريخ الانضمام</th>
                        <th class="p-2 text-right">رسوم</th>
                        <th class="p-2 text-right">الفاتورة</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y">
                    @forelse ($this->rows as $r)
                        <tr>
                            <td class="p-2">{{ $r['id'] }}</td>
                            <td class="p-2 font-mono">{{ $r['identity'] }}</td>
                            <td class="p-2">{{ $r['user'] }}</td>
                            <td class="p-2">{{ $r['email'] }}</td>
                            <td class="p-2">{{ $r['joined_at'] }}</td>
                            <td class="p-2">{{ number_format($r['fee']) }} IQD</td>
                            <td class="p-2">{{ $r['invoice_no'] ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="p-6 text-gray-500">لا توجد بيانات.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</x-filament::page>
