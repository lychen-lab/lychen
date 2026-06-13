import { type Composer, type I18n } from 'vue-i18n';
import { AVAILABLE_LOCALES, LOCAL_STORAGE_KEY, defaultFallbackLocale } from '../configs/Default';

export function useTrans(i18n: I18n) {
  // The app is always created with `legacy: false`, so the global context is a Composer.
  const global = i18n.global as Composer;

  function getDefaultLocale(): string {
    const fallback = global.fallbackLocale.value;
    return typeof fallback === 'string' ? fallback : defaultFallbackLocale;
  }

  function setCurrentLocale(newLocale: string) {
    return (global.locale.value = newLocale);
  }

  function getAvailableLocales(): string[] {
    return AVAILABLE_LOCALES;
  }

  function isLocaleSupported(locale: string | null | undefined): locale is string {
    if (!locale) {
      return false;
    }
    return getAvailableLocales().includes(locale);
  }

  function getUserLocale() {
    const locale =
      !import.meta.env.SSR && !import.meta.env.VITE_SSG
        ? window.navigator.language
        : getDefaultLocale();

    return {
      locale: locale,
      localeNoRegion: locale.split('-')[0] ?? locale,
    };
  }

  function getPersistedLocale() {
    if (import.meta.env.SSR || import.meta.env.VITE_SSG) {
      return null;
    }
    const persistedLocale = localStorage.getItem(LOCAL_STORAGE_KEY);

    if (isLocaleSupported(persistedLocale)) {
      return persistedLocale;
    } else {
      return null;
    }
  }

  function guessDefaultLocale() {
    const userPersistedLocale = getPersistedLocale();
    if (userPersistedLocale) {
      return userPersistedLocale;
    }

    const userPreferredLocale = getUserLocale();

    if (isLocaleSupported(userPreferredLocale.locale)) {
      return userPreferredLocale.locale;
    }

    if (isLocaleSupported(userPreferredLocale.localeNoRegion)) {
      return userPreferredLocale.localeNoRegion;
    }

    return getDefaultLocale();
  }

  async function switchLanguage(newLocale: string) {
    setCurrentLocale(newLocale);
    if (!import.meta.env.SSR && !import.meta.env.VITE_SSG) {
      localStorage.setItem(LOCAL_STORAGE_KEY, newLocale);
    }
  }

  return {
    switchLanguage,
    isLocaleSupported,
    guessDefaultLocale,
  };
}
