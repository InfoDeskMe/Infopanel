import { defineConfig } from 'vite'

// Deliberately not reusing vite.config.js's react() plugin here: that
// project pins @vitejs/plugin-react@1.x, which predates the version's
// `process.env.VITEST` check and injects a fast-refresh preamble that
// only works behind the real Vite dev server -- it crashes under
// Vitest's transform with "can't detect preamble". Vite's default
// esbuild JSX transform is enough for rendering components in tests.
export default defineConfig({
  esbuild: {
    jsx: 'automatic',
  },
  test: {
    environment: 'jsdom',
    setupFiles: ['./src/test/setup.js'],
    globals: true,
  },
})
