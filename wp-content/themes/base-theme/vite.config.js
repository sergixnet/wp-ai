import { defineConfig } from 'vite';
import { resolve } from 'path';

export default defineConfig({
  build: {
    // Output directory
    outDir: 'assets/dist',
    
    // Clear output directory before build
    emptyOutDir: true,
    
    // Generate sourcemaps for production
    sourcemap: true,
    
    // Rollup options
    rollupOptions: {
      input: {
        main: resolve(__dirname, 'assets/src/js/main.js'),
        editor: resolve(__dirname, 'assets/src/js/editor.js'),
        admin: resolve(__dirname, 'assets/src/js/admin.js'),
      },
      output: {
        entryFileNames: 'js/[name].js',
        chunkFileNames: 'js/[name]-[hash].js',
        assetFileNames: (assetInfo) => {
          if (assetInfo.name.endsWith('.css')) {
            return 'css/[name][extname]';
          }
          return 'assets/[name]-[hash][extname]';
        },
      },
    },
    
    // Minification
    minify: 'terser',
    terserOptions: {
      compress: {
        drop_console: true,
      },
    },
  },
  
  // CSS options
  css: {
    preprocessorOptions: {
      scss: {
        additionalData: `@import "./assets/src/scss/abstracts/_variables.scss";`
      }
    },
    devSourcemap: true,
  },
  
  // Dev server
  server: {
    host: '0.0.0.0',
    port: 3000,
    strictPort: false,
    open: false,
  },
  
  // Base public path
  base: './',
  
  // Resolve aliases
  resolve: {
    alias: {
      '@': resolve(__dirname, 'assets/src'),
      '@scss': resolve(__dirname, 'assets/src/scss'),
      '@js': resolve(__dirname, 'assets/src/js'),
    },
  },
});
