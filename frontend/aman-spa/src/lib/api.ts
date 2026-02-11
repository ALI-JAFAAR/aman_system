import axios from 'axios'

export const api = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL || '/api',
  withCredentials: true,
  headers: {
    'X-Requested-With': 'XMLHttpRequest',
    Accept: 'application/json',
  },
})

export async function initCsrf() {
  // Sanctum SPA flow
  await api.get('/sanctum/csrf-cookie', { baseURL: '' })
}

