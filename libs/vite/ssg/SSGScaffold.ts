import { ViteSSG } from 'vite-ssg';

import { type RouteRecordRaw } from 'vue-router';
import { useTrans } from '@lychen/vue-i18n/composables/useTrans';
import { createI18n } from '@lychen/vue-i18n/configs/createI18n';
import { buildConfigObject } from '@lychen/vue-router-configs/DefaultConfig';
import { useRouteMiddleware } from '@lychen/vue-i18n/composables/useRouteMiddleware';
import { AVAILABLE_LOCALES } from '@lychen/vue-i18n/configs/Default';

export function ViteSSGScaffold(app: Parameters<typeof ViteSSG>[0], routes: RouteRecordRaw[]) {
  return ViteSSG(
    app,
    // Route config only defines the /:locale? structure — no shared i18n guard here.
    buildConfigObject(routes),
    ({ app, router, routePath }) => {
      // Create a FRESH i18n instance for each call. vite-ssg renders up to 20 routes
      // concurrently: a shared singleton would cause locale race conditions between
      // parallel renders. Each render gets its own instance.
      const i18n = createI18n();
      const i18nUtilities = useTrans(i18n);

      // During SSR, derive the locale directly from the route path and pre-seed it
      // before renderToString runs. This is the authoritative locale source for SSR;
      // the beforeEach below is redundant for SSR but harmless.
      if (import.meta.env.SSR && routePath) {
        const locale = routePath.split('/')[1];
        if (locale && AVAILABLE_LOCALES.includes(locale)) {
          i18nUtilities.switchLanguage(locale);
        }
      }

      app.use(i18n);

      // Register the locale-switching guard using this render's i18n instance.
      // On the client this handles SPA navigation; on SSR it re-confirms the locale
      // extracted above when the router first pushes the route.
      if (router) {
        router.beforeEach(useRouteMiddleware(i18nUtilities).beforeEnter);
      }
    },
  );
}
