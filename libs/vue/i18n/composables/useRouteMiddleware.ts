import type { NavigationGuardNext, RouteLocationNormalizedGeneric } from 'vue-router';

import { useTrans } from '../composables/useTrans';

export function useRouteMiddleware(i18n: ReturnType<typeof useTrans>) {
  async function beforeEnter(
    to: RouteLocationNormalizedGeneric,
    _from: RouteLocationNormalizedGeneric,
    next: NavigationGuardNext,
  ) {
    const localeParam = to.params.locale;
    const paramLocale = Array.isArray(localeParam) ? localeParam[0] : localeParam;

    if (!i18n.isLocaleSupported(paramLocale)) {
      return next(i18n.guessDefaultLocale());
    }

    await i18n.switchLanguage(paramLocale);

    return next();
  }

  return { beforeEnter };
}
