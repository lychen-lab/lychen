import { type I18n } from 'vue-i18n';
import { LOCAL_STORAGE_KEY } from '../configs/Default';

export function useTrans(i18n: I18n) {
  function getDefaultLocale() {
    return i18n.global.fallbackLocale.value;
  }

  function setCurrentLocale(newLocale: string) {
    return (i18n.global.locale.value = newLocale);
  }

  function getAvailableLocales(): string[] {
    return i18n.global.availableLocales;
  }

  function isLocaleSupported(locale: string | null) {
    if (!locale) {
      return false;
    }
    return getAvailableLocales().includes(locale);
  }

  function getUserLocale() {
    const locale =
      !import.meta.env.SSR && !import.meta.env.VITE_SSG
        ? window.navigator.language || window.navigator.userLanguage
        : getDefaultLocale();

    return {
      locale: locale,
      localeNoRegion: locale.split('-')[0],
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
      document.querySelector('html').setAttribute('lang', newLocale);
      localStorage.setItem(LOCAL_STORAGE_KEY, newLocale);
    }
  }

  return {
    switchLanguage,
    isLocaleSupported,
    guessDefaultLocale,
  };
}
