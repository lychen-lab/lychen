import type { ViteSSGOptions } from 'vite-ssg';
import generateSitemap from 'vite-ssg-sitemap';
import type { RouteRecordRaw } from 'vue-router';

export const ssgOptions: ViteSSGOptions = {
  script: 'async',
  formatting: 'prettify',
  includedRoutes(paths: string[], routes: readonly RouteRecordRaw[]) {
    const locales = ['fr-FR', 'en-US'];
    return paths.flatMap((path) => {
      if (!path.includes(':locale?')) return path;
      return locales.map((locale) => path.replace(':locale?', locale));
    });
  },
  dirStyle: 'nested',
  onFinished() {
    const host = process.env.VITE_UNHEAD_HOST;
    if (!host) {
      throw new Error('VITE_UNHEAD_HOST environment variable is required for sitemap generation');
    }
    generateSitemap({ hostname: `https://${host}` });
  },
};
