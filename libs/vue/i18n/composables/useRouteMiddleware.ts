import { useTrans } from '../composables/useTrans';

export function useRouteMiddleware(i18n: ReturnType<typeof useTrans>) {
  async function beforeEnter(to, _from, next) {
    const paramLocale = to.params.locale;

    if (!i18n.isLocaleSupported(paramLocale)) {
      return next(i18n.guessDefaultLocale());
    }

    await i18n.switchLanguage(paramLocale);

    return next();
  }

  return { beforeEnter };
}
