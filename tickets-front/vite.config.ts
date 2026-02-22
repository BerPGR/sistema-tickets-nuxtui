import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import ui from "@nuxt/ui/vite"
import * as path from 'path'
import { fileURLToPath } from "node:url"

// https://vite.dev/config/
export default defineConfig({
  plugins: [
    vue(),
    ui(),
  ],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url))
    }
  }
})
