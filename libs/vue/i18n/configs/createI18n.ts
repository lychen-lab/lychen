import { createI18n as defaultCreateI18n, type I18nOptions, type I18n } from 'vue-i18n';
import { configSSG } from './ConfigSSG';
import { configDefault } from './ConfigDefault';

export function createI18n(config?: I18nOptions): I18n {
  if (!config) {
    // Use SSR-safe config during server-side rendering.
    // Note: import.meta.env.VITE_SSG is unreliable in SSR (transformed by
    // vite-plugin-env-runtime to window.__PRODUCTION__APP__CONF__.VITE_SSG
    // which is absent from the SSR define map), so we check SSR alone.
    if (import.meta.env.SSR) {
      config = configSSG();
    } else {
      config = configDefault();
    }
  }

  return defaultCreateI18n(config);
}
