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
    port: 5800,
  },
  plugins: [
    vueDevTools(),
    tailwindcss(),
    // mkcert downloads and executes a shared binary; concurrent vitest/CI workers race on it (ETXTBSY) and dev certs are pointless there
    !process.env.VITEST &&
      !process.env.CI &&
      mkcert({
        hosts: ['app.tera.lychen.local'],
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
        name: 'Tera',
        short_name: 'Tera',
        description: 'Tera application from Lychen ecosystem',
        theme_color: '#ffffff',
        display: 'standalone',
        icons: [
          {
            src: 'logos/tera/pwa-64x64.png',
            sizes: '64x64',
            type: 'image/png',
          },
          {
            src: 'logos/tera/pwa-192x192.png',
            sizes: '192x192',
            type: 'image/png',
          },
          {
            src: 'logos/tera/pwa-512x512.png',
            sizes: '512x512',
            type: 'image/png',
          },
          {
            src: 'logos/tera/maskable-icon-512x512.png',
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
