import { ViteSSG } from 'vite-ssg';

import { type RouteRecordRaw } from 'vue-router';
import { useTrans } from '@lychen/vue-i18n/composables/useTrans';
import { createI18n } from '@lychen/vue-i18n/configs/createI18n';
import { buildConfigObject } from '@lychen/vue-router-configs/DefaultConfig';
import { useRouteMiddleware } from '@lychen/vue-i18n/composables/useRouteMiddleware';

export function ViteSSGScaffold(app: Parameters<typeof ViteSSG>[0], routes: RouteRecordRaw[]) {
  const i18n = createI18n();

  const i18nUtilities = useTrans(i18n);

  return ViteSSG(
    app,
    buildConfigObject(routes, i18nUtilities, useRouteMiddleware(i18nUtilities).beforeEnter),
    ({ app, router, routes, isClient, initialState }) => {
      app.use(i18n);
    },
  );
}
