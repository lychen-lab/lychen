import { ViteSSG } from 'vite-ssg';

import { RouterView, type RouteRecordRaw } from 'vue-router';
import { useTrans } from '@lychen/vue-i18n/composables/useTrans';
import { createI18n } from '@lychen/vue-i18n/configs/createI18n';
import { useRouteMiddleware } from '@lychen/vue-i18n/composables/useRouteMiddleware';

export function ViteSSGScaffold(app: Parameters<typeof ViteSSG>[0], routes: RouteRecordRaw[]) {
  const i18n = createI18n();

  const i18nUtilities = useTrans(i18n);

  function buildConfigObject(routes: RouteRecordRaw[], i18nUtilities: unknown) {
    const localizedRoutes: RouteRecordRaw[] = [
      {
        path: '/:locale?',
        component: RouterView,
        beforeEnter: useRouteMiddleware(i18nUtilities).beforeEnter,
        children: routes,
      },
    ];

    return {
      routes: localizedRoutes,
    };
  }

  return ViteSSG(
    app,
    buildConfigObject(routes, i18nUtilities),
    ({ app, router, routes, isClient, initialState }) => {
      app.use(i18n);
    },
  );
}
