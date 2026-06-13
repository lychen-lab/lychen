import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import tailwindcss from '@tailwindcss/vite';
import mkcert from 'vite-plugin-mkcert';

export default defineConfig({
  plugins: [
    vue(),
    tailwindcss(),
    // mkcert downloads and executes a shared binary; concurrent vitest/CI workers race on it (ETXTBSY) and dev certs are pointless there
    !process.env.VITEST &&
      !process.env.CI &&
      mkcert({
        hosts: ['storybook.lychen.local'],
      }),
  ],
});
