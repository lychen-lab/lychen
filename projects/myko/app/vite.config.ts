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
    port: 5820,
  },
  plugins: [
    tailwindcss(),
    // The fake login's switch is resolved at build time, never at runtime: a production build
    // replaces it with undefined, so neither the variable nor the fake login reach dist/.
    EnvRuntime({ exclude: ['VITE_MYKO_FAKE_AUTH'] }),
    // mkcert downloads and executes a shared binary; concurrent vitest/CI workers race on it (ETXTBSY) and dev certs are pointless there
    !process.env.VITEST &&
      !process.env.CI &&
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
        description: 'Myko, mushroom cultivation from the Lychen ecosystem',
        theme_color: '#ffffff',
        display: 'standalone',
        icons: [
          {
            src: 'icons/pwa-64x64.png',
            sizes: '64x64',
            type: 'image/png',
          },
          {
            src: 'icons/pwa-192x192.png',
            sizes: '192x192',
            type: 'image/png',
          },
          {
            src: 'icons/pwa-512x512.png',
            sizes: '512x512',
            type: 'image/png',
          },
          {
            src: 'icons/maskable-icon-512x512.png',
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
      '@': path.resolve(import.meta.dirname, './src'),
    },
  },
  build: {
    sourcemap: true,
    rolldownOptions: {
      output: {
        // The maps give files and lines, not the sources: a source holds the development-only
        // switch of the fake login (src/auth/createAuth.ts), which dist/ must never contain.
        sourcemapExcludeSources: true,
      },
    },
  },
};

export default config;
