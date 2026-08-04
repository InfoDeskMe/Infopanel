import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [react(
    { include: "**/*.jsx", }
  )],
  server: {
    watch: {
      usePolling: true
    },
    // In dev, Vite serves the panel directly (no nginx in front of it), so
    // it has to proxy /api itself to reach the backend -- same role nginx
    // plays in production via the location block in default.conf.
    proxy: {
      '/api': {
        target: 'http://nginx:8080',
        changeOrigin: true,
      },
    },
  }
})
