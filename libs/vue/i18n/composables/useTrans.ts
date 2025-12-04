import { useI18n } from 'vue-i18n';

export function useTrans() {
  const LOCAL_STORAGE_KEY = 'user-locale';

  const { locale, availableLocales, fallbackLocale } = useI18n();

  function isLocaleSupported(locale: string | null) {
    if (!locale) {
      return false;
    }
    return availableLocales.includes(locale);
  }

  function getUserLocale() {
    const locale =
      window.navigator.language || window.navigator.userLanguage || fallbackLocale.value;

    return {
      locale: locale,
      localeNoRegion: locale.split('-')[0],
    };
  }

  function getPersistedLocale() {
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

    return fallbackLocale;
  }

  async function switchLanguage(newLocale: string) {
    locale.value = newLocale;
    document.querySelector('html').setAttribute('lang', newLocale);
    localStorage.setItem(LOCAL_STORAGE_KEY, newLocale);
  }

  async function routeMiddleware(to, _from, next) {
    const paramLocale = to.params.locale;

    if (!isLocaleSupported(paramLocale)) {
      return next(guessDefaultLocale());
    }

    await switchLanguage(paramLocale);

    return next();
  }

  return {
    switchLanguage,
    routeMiddleware,
  };
}
