import path from 'node:path';

import type { UserConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import mkcert from 'vite-plugin-mkcert';
import { VitePWA } from 'vite-plugin-pwa';
import tailwindcss from '@tailwindcss/vite';
import EnvRuntime from 'vite-plugin-env-runtime';

const config: UserConfig = {
  server: {
    https: {},
    port: 5815,
  },
  plugins: [
    tailwindcss(),
    EnvRuntime(),
    mkcert({
      hosts: ['app.myko.lychen.local'],
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
        name: 'Myko',
        short_name: 'Myko',
        description: 'Myko application from Lychen ecosystem',
        theme_color: '#ffffff',
        display: 'standalone',
        icons: [
          {
            src: 'logos/myko/pwa-64x64.png',
            sizes: '64x64',
            type: 'image/png',
          },
          {
            src: 'logos/myko/pwa-192x192.png',
            sizes: '192x192',
            type: 'image/png',
          },
          {
            src: 'logos/myko/pwa-512x512.png',
            sizes: '512x512',
            type: 'image/png',
          },
          {
            src: 'logos/myko/maskable-icon-512x512.png',
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
