import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [vue()],
  root: '.',
  base: '/build/',
  publicDir: false,

  build: {
    assetsDir: '',
    emptyOutDir: true,
    manifest: true,
    outDir: './public/build',
    rollupOptions: {
      input: {
        app: './assets/app.ts',
      },
    },
  },
  resolve: {
    alias: {
      vue: 'vue/dist/vue.esm-bundler.js',
    },
  },
})
