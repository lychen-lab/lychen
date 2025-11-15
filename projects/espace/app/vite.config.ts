import path from 'node:path';

import type { UserConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import mkcert from 'vite-plugin-mkcert';
import { VitePWA } from 'vite-plugin-pwa';
import tailwindcss from '@tailwindcss/vite';
import vueDevTools from 'vite-plugin-vue-devtools';

const config: UserConfig = {
  server: {
    https: {},
    port: 5810,
  },
  plugins: [
    vueDevTools(),
    tailwindcss(),
    mkcert({
      hosts: ['app.espace.lychen.local'],
    }),
    vue({
      template: {
        transformAssetUrls: {
          includeAbsolute: false,
        },
      },
    }),
    VitePWA({
      registerType: 'autoUpdate',
      manifest: {
        name: 'Espace',
        short_name: 'Espace',
        description: 'Espace application from Lychen ecosystem',
        theme_color: '#ffffff',
        display: 'standalone',
        icons: [
          {
            src: 'logos/espace/pwa-64x64.png',
            sizes: '64x64',
            type: 'image/png',
          },
          {
            src: 'logos/espace/pwa-192x192.png',
            sizes: '192x192',
            type: 'image/png',
          },
          {
            src: 'logos/espace/pwa-512x512.png',
            sizes: '512x512',
            type: 'image/png',
          },
          {
            src: 'logos/espace/maskable-icon-512x512.png',
            sizes: '512x512',
            type: 'image/png',
            purpose: 'maskable',
          },
        ],
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
};

export default config;
