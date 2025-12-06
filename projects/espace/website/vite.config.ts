import path from 'node:path';

import type { UserConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import mkcert from 'vite-plugin-mkcert';
import tailwindcss from '@tailwindcss/vite';
import vueDevTools from 'vite-plugin-vue-devtools';
import type { ViteSSGOptions } from 'vite-ssg';
import { ssgOptions } from '@lychen/vite-ssg/ssgOptions';

const config: UserConfig & ViteSSGOptions = {
  server: {
    https: {},
    port: 5145,
  },
  plugins: [
    vueDevTools(),
    tailwindcss(),
    mkcert({
      hosts: [process.env.VITE_UNHEAD_HOST || 'localhost'],
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
  ssgOptions,
};

export default config;
