<script setup lang="ts">
import { useAuthStore } from '../stores/auth'

const auth = useAuthStore()
</script>

<template>
  <div class="min-h-screen p-6">
    <div class="max-w-4xl mx-auto">
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-slate-900">لوحة التحكم</h1>
        <button
          class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm"
          @click="auth.logout()"
        >
          تسجيل الخروج
        </button>
      </div>

      <div class="bg-white border border-slate-200 rounded-2xl p-6">
        <p class="text-slate-700">
          مرحباً <span class="font-semibold">{{ auth.user?.name }}</span>
          ({{ auth.user?.email }})
        </p>
        <p class="mt-2 text-sm text-slate-500">هذه صفحة تجريبية كبداية. سنضيف وحدات النظام هنا.</p>
      </div>
    </div>
  </div>
</template>

