<script setup lang="ts">
import { useAuthStore } from '../stores/auth'

const auth = useAuthStore()
</script>

<template>
  <div class="min-h-screen bg-slate-50">
    <div class="max-w-7xl mx-auto p-4 lg:p-6">
      <div class="grid grid-cols-1 lg:grid-cols-[280px_1fr] gap-4">
        <aside class="bg-white border border-slate-200 rounded-2xl p-4">
          <div class="mb-4">
            <div class="text-lg font-semibold text-slate-900">منصة أمان</div>
            <div class="text-xs text-slate-500 mt-1">{{ auth.user?.email }}</div>
          </div>

          <nav class="space-y-1">
            <RouterLink class="nav-link" to="/app">الرئيسية</RouterLink>
            <RouterLink class="nav-link" to="/app/affiliations">الانتسابات</RouterLink>
            <RouterLink class="nav-link" to="/app/claims">المطالبات</RouterLink>
            <RouterLink class="nav-link" to="/app/insurance">طلبات التأمين</RouterLink>
            <RouterLink class="nav-link" to="/app/wallet">المحفظة</RouterLink>
            <RouterLink class="nav-link" to="/app/reports">التقارير</RouterLink>
          </nav>

          <button
            class="mt-6 w-full rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm"
            @click="auth.logout()"
          >
            تسجيل الخروج
          </button>
        </aside>

        <main class="bg-white border border-slate-200 rounded-2xl p-4 lg:p-6">
          <RouterView />
        </main>
      </div>
    </div>
  </div>
</template>

<style scoped>
.nav-link {
  display: block;
  padding: 10px 12px;
  border-radius: 12px;
  color: #0f172a;
}
.nav-link.router-link-active {
  background: #0f172a;
  color: #fff;
}
</style>

