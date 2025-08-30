<x-filament::page>
    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3">
        <div>
            <label class="text-sm text-gray-600">الشريك</label>
            <select name="orgId" class="w-full rounded-lg border-gray-300">
                <option value="">— اختر —</option>
                @foreach ($this->partners as $org)
                    <option value="{{ $org->id }}" @selected(request('orgId') == $org->id)>{{ $org->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="text-sm text-gray-600">من</label>
            <input type="date" name="from" value="{{ $this->from }}" class="w-full rounded-lg border-gray-300">
        </div>
        <div>
            <label class="text-sm text-gray-600">إلى</label>
            <input type="date" name="to" value="{{ $this->to }}" class="w-full rounded-lg border-gray-300">
        </div>
        <div class="flex items-end">
            <button class="px-4 py-2 rounded-lg bg-primary-600 text-white">عرض</button>
        </div>
    </form>

    @if ($this->orgId)
        <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-4">
            <div class="rounded-xl border p-4">
                <div class="text-sm text-gray-500">رصيد أول المدة (2100)</div>
                <div class="text-2xl font-semibold">{{ number_format($this->opening) }} IQD</div>
            </div>
            <div class="rounded-xl border p-4">
                <div class="text-sm text-gray-500">إجمالي مدين</div>
                <div class="text-2xl font-semibold">{{ number_format($this->totals['debit']) }} IQD</div>
            </div>
            <div class="rounded-xl border p-4">
                <div class="text-sm text-gray-500">إجمالي دائن / رصيد آخر</div>
                <div class="text-2xl font-semibold">{{ number_format($this->totals['closing']) }} IQD</div>
            </div>
        </div>

        <div class="mt-6 rounded-xl border overflow-hidden">
            <div class="px-4 py-3 bg-gray-50 dark:bg-gray-800">
                <h3 class="font-semibold">حركة المدة</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                    <tr class="bg-gray-50 dark:bg-gray-900/60">
                        <th class="p-2 text-right">تاريخ</th>
                        <th class="p-2 text-right">فاتورة</th>
                        <th class="p-2 text-right">وصف</th>
                        <th class="p-2 text-right">مدين</th>
                        <th class="p-2 text-right">دائن</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y">
                    @forelse ($this->rows as $r)
                        <tr>
                            <td class="p-2">{{ $r['date'] }}</td>
                            <td class="p-2">{{ $r['inv'] }}</td>
                            <td class="p-2">{{ $r['desc'] }}</td>
                            <td class="p-2 text-right">{{ $r['debit'] ? number_format($r['debit']) : '—' }}</td>
                            <td class="p-2 text-right">{{ $r['credit'] ? number_format($r['credit']) : '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="p-6 text-gray-500">لا توجد بيانات.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Form التسوية --}}
        <div class="mt-6 rounded-xl border p-4">
            <h3 class="font-semibold mb-3">تسوية رصيد الشريك</h3>
            <form wire:submit.prevent="settle" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <div>
                    <label class="text-sm text-gray-600">المبلغ</label>
                    <input type="number" step="0.01" wire:model="amount" class="w-full rounded-lg border-gray-300">
                    @error('amount') <div class="text-rose-600 text-xs mt-1">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="text-sm text-gray-600">الطريقة</label>
                    <select wire:model="method" class="w-full rounded-lg border-gray-300">
                        <option value="cash">نقدًا</option>
                        <option value="pos">POS</option>
                        <option value="zaincash">زين كاش</option>
                        <option value="bank">تحويل بنكي</option>
                    </select>
                </div>
                <div>
                    <label class="text-sm text-gray-600">تاريخ القيد</label>
                    <input type="date" wire:model="posted_at" class="w-full rounded-lg border-gray-300">
                </div>
                <div class="md:col-span-4">
                    <label class="text-sm text-gray-600">ملاحظة</label>
                    <input type="text" wire:model="note" class="w-full rounded-lg border-gray-300">
                </div>
                <div class="md:col-span-4">
                    <button class="px-4 py-2 rounded-lg bg-primary-600 text-white">تسجيل التسوية</button>
                </div>
            </form>
        </div>
    @endif
</x-filament::page>
