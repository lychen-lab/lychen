import path from 'node:path';
import vue from '@vitejs/plugin-vue';
import mkcert from 'vite-plugin-mkcert';
import generateSitemap from 'vite-ssg-sitemap';
import tailwindcss from '@tailwindcss/vite';
import vueDevTools from 'vite-plugin-vue-devtools';
import type { UserConfig } from 'vite';
import type { ViteSSGOptions } from 'vite-ssg';

const config: UserConfig & ViteSSGOptions = {
  server: {
    https: {},
    port: 5140,
  },
  plugins: [
    vueDevTools(),
    tailwindcss(),
    mkcert({
      hosts: ['lychen.local'],
    }),
    vue({
      template: {
        transformAssetUrls: {
          includeAbsolute: false,
        },
      },
    }),
  ],
  resolve: {
    alias: {
      '@': path.resolve(__dirname, './src'),
    },
  },
  build: {
    sourcemap: true,
  },
  ssgOptions: {
    script: 'async',
    formatting: 'prettify',
    includedRoutes(paths, routes) {
      const locales = ['fr-FR', 'en-US']; // Your supported locales
      return paths.flatMap((path) => {
        if (!path.includes(':locale?')) return path;
        return locales.map((locale) => path.replace(':locale?', locale));
      });
    },
    onFinished() {
      generateSitemap({ hostname: `https://${process.env.VITE_UNHEAD_HOST}` });
    },
  },
};

export default config;
