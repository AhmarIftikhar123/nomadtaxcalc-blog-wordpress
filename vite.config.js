import { defineConfig } from 'vite'
import { resolve } from 'path'

export default defineConfig({
  build: {
    outDir: 'dist',
    rollupOptions: {
      input: {
        main: resolve(__dirname, 'src/js/main.js'),
        style: resolve(__dirname, 'src/scss/main.scss'),
      },
      output: {
        assetFileNames: 'css/[name][extname]',
        entryFileNames: 'js/[name].js',
      }
    },
    // Disable hashing for easy WordPress enqueuing
    cssCodeSplit: true,
  },
  css: {
    preprocessorOptions: {
      scss: {
        // Global SCSS variables available everywhere
        additionalData: `@use "./src/scss/abstracts/abstracts-helpers/variables" as *;`
      }
    }
  }
})
