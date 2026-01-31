import path from 'node:path';
import { defineConfig, type UserConfig } from 'vite'; // Added defineConfig
import vue from '@vitejs/plugin-vue';
import mkcert from 'vite-plugin-mkcert';
import tailwindcss from '@tailwindcss/vite';
import vueDevTools from 'vite-plugin-vue-devtools';
import type { ViteSSGOptions } from 'vite-ssg';
import { ssgOptions } from '@lychen/vite-ssg/ssgOptions';
import EnvRuntime from 'vite-plugin-env-runtime';

// Wrap the config in defineConfig to access build flags
export default defineConfig(({ isSsrBuild }) => {
  const config: UserConfig & ViteSSGOptions = {
    server: {
      https: {},
      port: 5145,
    },
    define: {
      // 1. During SSG Build (isSsrBuild === true):
      //    We replace it with an empty object so Node.js doesn't crash.
      // 2. During Client Build (isSsrBuild === false):
      //    We define it as itself (a string), which tells Vite: "Leave this as a global variable".
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
