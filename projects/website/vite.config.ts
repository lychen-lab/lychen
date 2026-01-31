import path from 'node:path';
import { defineConfig, type UserConfig } from 'vite'; // Added defineConfig
import vue from '@vitejs/plugin-vue';
import mkcert from 'vite-plugin-mkcert';
import tailwindcss from '@tailwindcss/vite';
import vueDevTools from 'vite-plugin-vue-devtools';
import type { ViteSSGOptions } from 'vite-ssg';
import { ssgOptions } from '@lychen/vite-ssg/ssgOptions';
import EnvRuntime from 'vite-plugin-env-runtime';

export default defineConfig(({ isSsrBuild }) => {
  const config: UserConfig & ViteSSGOptions = {
    server: {
      https: {},
      port: 5140,
    },
    define: {
      'window.__PRODUCTION__APP__CONF__': isSsrBuild
        ? JSON.stringify({})
        : 'window.__PRODUCTION__APP__CONF__',
    },
    plugins: [
      vueDevTools(),
      tailwindcss(),
      EnvRuntime(),
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

  return config;
});
