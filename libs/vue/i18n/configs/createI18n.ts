import { createI18n as defaultCreateI18n, type I18nOptions, type I18n } from 'vue-i18n';
import { configSSG } from './ConfigSSG';
import { configDefault } from './ConfigDefault';

export function createI18n(config?: I18nOptions): I18n {
  if (!config) {
    if (import.meta.env.SSR && import.meta.env.VITE_SSG) {
      config = configSSG();
    } else {
      config = configDefault();
    }
  }

  return defaultCreateI18n(config);
}
