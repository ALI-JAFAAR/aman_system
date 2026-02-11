import { defineStore } from 'pinia'
import { api, initCsrf } from '../lib/api'

export type AuthUser = {
  id: number
  name: string
  email: string
  roles: string[]
}

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null as AuthUser | null,
    loading: false,
  }),
  actions: {
    async fetchMe() {
      this.loading = true
      try {
        const { data } = await api.get('/v1/auth/me')
        this.user = data?.data?.user ?? null
      } finally {
        this.loading = false
      }
    },
    async login(email: string, password: string) {
      await initCsrf()
      const { data } = await api.post('/v1/auth/login', { email, password })
      this.user = data?.data?.user ?? null
    },
    async logout() {
      await api.post('/v1/auth/logout')
      this.user = null
    },
  },
})

