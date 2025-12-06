import path from 'node:path';
import vue from '@vitejs/plugin-vue';
import mkcert from 'vite-plugin-mkcert';
import tailwindcss from '@tailwindcss/vite';
import vueDevTools from 'vite-plugin-vue-devtools';
import type { UserConfig } from 'vite';
import type { ViteSSGOptions } from 'vite-ssg';
import { ssgOptions } from '@lychen/vite-ssg/ssgOptions';

const config: UserConfig & ViteSSGOptions = {
  server: {
    https: {},
    port: 5140,
  },
  plugins: [
    vueDevTools(),
    tailwindcss(),
    mkcert({
      hosts: [<string>process.env.VITE_UNHEAD_HOST],
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
  ssgOptions,
};

export default config;
