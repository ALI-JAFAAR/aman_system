import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

// https://vite.dev/config/
export default defineConfig({
  // When built, files are copied to Laravel `public/spa/`,
  // so asset URLs must be rooted at `/spa/`.
  base: '/spa/',
  plugins: [vue()],
  server: {
    // During local dev, proxy API requests to Laravel.
    proxy: {
      '/api': {
        target: 'http://localhost:8000',
        changeOrigin: true,
      },
      '/sanctum': {
        target: 'http://localhost:8000',
        changeOrigin: true,
      },
    },
  },
})
