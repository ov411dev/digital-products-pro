import { defineConfig } from 'vite';
import { resolve } from 'node:path';
const themeRoot = resolve(process.cwd(), 'theme/digital-products-pro');
export default defineConfig({
  build: {
    outDir: resolve(themeRoot, 'dist'),
    emptyOutDir: true,
    manifest: 'manifest.json',
    rollupOptions: { input: {
      main: resolve(themeRoot, 'assets/js/main.js'),
      styles: resolve(themeRoot, 'assets/css/main.css')
    }}
  }
});
