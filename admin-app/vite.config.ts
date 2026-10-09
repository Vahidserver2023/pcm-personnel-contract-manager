import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import path from 'path';

export default defineConfig({
  plugins: [react()],
  resolve: {
    alias: {
      '@': path.resolve(__dirname, './src'),
    },
  },
  build: {
    outDir: '../plugin/personnel-contract-manager/admin-dist',
    emptyOutDir: true,
  },
  server: {
    proxy: {
      '/wp-json': {
        target: 'http://localhost:8000',
        changeOrigin: true,
      },
    },
  },
});
