<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { api } from '../lib/api'

type InsuranceRequest = {
  id: number
  status: string
  partner_filled_number?: string | null
  platform_generated_number?: string | null
}

const loading = ref(false)
const error = ref<string | null>(null)
const rows = ref<InsuranceRequest[]>([])

async function load() {
  loading.value = true
  error.value = null
  try {
    const { data } = await api.get('/v1/insurance/requests', { params: { per_page: 20 } })
    rows.value = data?.data?.data ?? []
  } catch (e: any) {
    error.value = e?.response?.data?.message ?? 'فشل تحميل الطلبات'
  } finally {
    loading.value = false
  }
}

onMounted(load)
</script>

<template>
  <div>
    <div class="flex items-center justify-between mb-4">
      <h1 class="text-xl font-semibold text-slate-900">طلبات التأمين</h1>
      <button class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm" @click="load">تحديث</button>
    </div>

    <p v-if="error" class="text-sm text-red-600 mb-3">{{ error }}</p>
    <p v-if="loading" class="text-sm text-slate-600">جاري التحميل...</p>

    <div v-else class="overflow-x-auto border border-slate-200 rounded-xl">
      <table class="min-w-full text-sm">
        <thead class="bg-slate-50 text-slate-600">
          <tr>
            <th class="text-right p-3">#</th>
            <th class="text-right p-3">الحالة</th>
            <th class="text-right p-3">رقم المنصة</th>
            <th class="text-right p-3">رقم الشريك</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="r in rows" :key="r.id" class="border-t border-slate-200">
            <td class="p-3">{{ r.id }}</td>
            <td class="p-3">{{ r.status }}</td>
            <td class="p-3">{{ r.platform_generated_number ?? '-' }}</td>
            <td class="p-3">{{ r.partner_filled_number ?? '-' }}</td>
          </tr>
          <tr v-if="rows.length === 0">
            <td class="p-3 text-slate-500" colspan="4">لا توجد بيانات.</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

