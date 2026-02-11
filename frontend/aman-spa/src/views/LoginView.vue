<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const auth = useAuthStore()
const router = useRouter()

const email = ref('')
const password = ref('')
const error = ref<string | null>(null)
const loading = ref(false)

async function submit() {
  error.value = null
  loading.value = true
  try {
    await auth.login(email.value, password.value)
    await router.push({ name: 'dashboard' })
  } catch (e: any) {
    error.value = e?.response?.data?.message ?? 'فشل تسجيل الدخول'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center p-6">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
      <h1 class="text-2xl font-semibold text-slate-900 mb-6">تسجيل الدخول</h1>

      <form class="space-y-4" @submit.prevent="submit">
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">البريد الإلكتروني</label>
          <input
            v-model="email"
            type="email"
            autocomplete="email"
            class="w-full rounded-xl border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-slate-900/20"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1">كلمة المرور</label>
          <input
            v-model="password"
            type="password"
            autocomplete="current-password"
            class="w-full rounded-xl border border-slate-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-slate-900/20"
          />
        </div>

        <p v-if="error" class="text-sm text-red-600">{{ error }}</p>

        <button
          type="submit"
          :disabled="loading"
          class="w-full rounded-xl bg-slate-900 text-white py-2 font-medium disabled:opacity-60"
        >
          {{ loading ? '...' : 'دخول' }}
        </button>
      </form>
    </div>
  </div>
</template>

