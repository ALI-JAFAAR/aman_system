import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const LoginView = () => import('../views/LoginView.vue')
const AppLayout = () => import('../layouts/AppLayout.vue')
const DashboardView = () => import('../views/DashboardView.vue')
const AffiliationsView = () => import('../views/AffiliationsView.vue')
const ClaimsView = () => import('../views/ClaimsView.vue')
const InsuranceRequestsView = () => import('../views/InsuranceRequestsView.vue')
const WalletView = () => import('../views/WalletView.vue')
const ReportsView = () => import('../views/ReportsView.vue')

export const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/', redirect: '/dashboard' },
    { path: '/login', name: 'login', component: LoginView, meta: { guestOnly: true } },
    { path: '/dashboard', redirect: '/app' },
    {
      path: '/app',
      component: AppLayout,
      meta: { requiresAuth: true },
      children: [
        { path: '', name: 'dashboard', component: DashboardView },
        { path: 'affiliations', name: 'affiliations', component: AffiliationsView },
        { path: 'claims', name: 'claims', component: ClaimsView },
        { path: 'insurance', name: 'insurance', component: InsuranceRequestsView },
        { path: 'wallet', name: 'wallet', component: WalletView },
        { path: 'reports', name: 'reports', component: ReportsView },
      ],
    },
  ],
})

router.beforeEach(async (to) => {
  const auth = useAuthStore()
  if (auth.user === null) {
    // Try to hydrate session if available
    try {
      await auth.fetchMe()
    } catch {
      // ignore
    }
  }

  if (to.meta.requiresAuth && !auth.user) return { name: 'login' }
  if (to.meta.guestOnly && auth.user) return { name: 'dashboard' }
})

