<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { api } from '../lib/api'

type Wallet = {
  id: number
  balance: number
  currency: string
}

const wallet = ref<Wallet | null>(null)
const loading = ref(false)
const error = ref<string | null>(null)

const toWalletId = ref<number | null>(null)
const amount = ref<number | null>(null)
const transferMsg = ref<string | null>(null)

async function load() {
  loading.value = true
  error.value = null
  try {
    const { data } = await api.get('/v1/wallet')
    wallet.value = data?.data ?? null
  } catch (e: any) {
    error.value = e?.response?.data?.message ?? 'فشل تحميل المحفظة'
  } finally {
    loading.value = false
  }
}

async function transfer() {
  transferMsg.value = null
  error.value = null
  try {
    const { data } = await api.post('/v1/wallet/transfers', {
      to_wallet_id: toWalletId.value,
      amount: amount.value,
    })
    transferMsg.value = data?.message ?? 'تم التحويل'
    await load()
  } catch (e: any) {
    error.value = e?.response?.data?.message ?? 'فشل التحويل'
  }
}

onMounted(load)
</script>

<template>
  <div>
    <div class="flex items-center justify-between mb-4">
      <h1 class="text-xl font-semibold text-slate-900">المحفظة</h1>
      <button class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm" @click="load">تحديث</button>
    </div>

    <p v-if="error" class="text-sm text-red-600 mb-3">{{ error }}</p>
    <p v-if="transferMsg" class="text-sm text-emerald-700 mb-3">{{ transferMsg }}</p>
    <p v-if="loading" class="text-sm text-slate-600">جاري التحميل...</p>

    <div v-else class="space-y-4">
      <div class="border border-slate-200 rounded-2xl p-4">
        <div class="text-sm text-slate-500">الرصيد</div>
        <div class="text-2xl font-semibold text-slate-900">
          {{ wallet?.balance ?? 0 }} {{ wallet?.currency ?? 'IQD' }}
        </div>
      </div>

      <div class="border border-slate-200 rounded-2xl p-4">
        <h2 class="font-semibold text-slate-900 mb-3">تحويل</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
          <div>
            <label class="block text-sm text-slate-700 mb-1">محفظة المستلم (ID)</label>
            <input v-model.number="toWalletId" type="number" class="w-full rounded-xl border border-slate-300 px-3 py-2" />
          </div>
          <div>
            <label class="block text-sm text-slate-700 mb-1">المبلغ</label>
            <input v-model.number="amount" type="number" step="0.01" class="w-full rounded-xl border border-slate-300 px-3 py-2" />
          </div>
        </div>
        <button class="mt-4 rounded-xl bg-slate-900 text-white px-4 py-2 text-sm" @click="transfer">إرسال</button>
      </div>
    </div>
  </div>
</template>

