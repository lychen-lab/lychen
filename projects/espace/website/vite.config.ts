import path from 'node:path';

import type { UserConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import mkcert from 'vite-plugin-mkcert';
import generateSitemap from 'vite-ssg-sitemap';
import tailwindcss from '@tailwindcss/vite';
import vueDevTools from 'vite-plugin-vue-devtools';
import type { ViteSSGOptions } from 'vite-ssg';
import type { RouteRecordRaw } from 'vue-router';

const config: UserConfig & ViteSSGOptions = {
  server: {
    https: {},
    port: 5145,
  },
  plugins: [
    vueDevTools(),
    tailwindcss(),
    mkcert({
      hosts: ['espace.lychen.local'],
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
  ssgOptions: {
    script: 'async',
    formatting: 'prettify',
    includedRoutes(paths: string[], routes: readonly RouteRecordRaw[]) {
      const locales = ['fr-FR', 'en-US']; // Your supported locales
      return paths.flatMap((path) => {
        if (!path.includes(':locale?')) return path;
        return locales.map((locale) => path.replace(':locale?', locale));
      });
    },
    dirStyle: 'nested',
    onFinished() {
      generateSitemap({ hostname: `https://${process.env.VITE_UNHEAD_HOST}` });
    },
  },
};

export default config;
