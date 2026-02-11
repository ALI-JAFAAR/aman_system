<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { api } from '../lib/api'

type Row = { id: number; user_id: number; organization_id: number; status?: string }

const rows = ref<Row[]>([])
const loading = ref(false)
const error = ref<string | null>(null)

async function load() {
  loading.value = true
  error.value = null
  try {
    const { data } = await api.get('/v1/UserAffiliation', { params: { per_page: 20 } })
    rows.value = data?.data?.data ?? []
  } catch (e: any) {
    error.value = e?.response?.data?.message ?? 'فشل تحميل الانتسابات'
  } finally {
    loading.value = false
  }
}

onMounted(load)
</script>

<template>
  <div>
    <div class="flex items-center justify-between mb-4">
      <h1 class="text-xl font-semibold text-slate-900">الانتسابات</h1>
      <button class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm" @click="load">تحديث</button>
    </div>

    <p v-if="error" class="text-sm text-red-600 mb-3">{{ error }}</p>
    <p v-if="loading" class="text-sm text-slate-600">جاري التحميل...</p>

    <div v-else class="overflow-x-auto border border-slate-200 rounded-xl">
      <table class="min-w-full text-sm">
        <thead class="bg-slate-50 text-slate-600">
          <tr>
            <th class="text-right p-3">#</th>
            <th class="text-right p-3">User</th>
            <th class="text-right p-3">Organization</th>
            <th class="text-right p-3">Status</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="r in rows" :key="r.id" class="border-t border-slate-200">
            <td class="p-3">{{ r.id }}</td>
            <td class="p-3">{{ r.user_id }}</td>
            <td class="p-3">{{ r.organization_id }}</td>
            <td class="p-3">{{ r.status ?? '-' }}</td>
          </tr>
          <tr v-if="rows.length === 0">
            <td class="p-3 text-slate-500" colspan="4">لا توجد بيانات.</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

