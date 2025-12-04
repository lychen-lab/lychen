import { RouterView, type RouteRecordRaw } from 'vue-router';
import { scrollBehavior } from './ScrollBehavior';

export function buildConfigObject(routes: RouteRecordRaw[], i18nUtilities: unknown) {
  const localizedRoutes: RouteRecordRaw[] = [
    {
      path: '/:locale?',
      component: RouterView,
      beforeEnter: useRouteMiddleware(i18nUtilities).beforeEnter,
      children: routes,
    },
  ];

  return {
    scrollBehavior,
    routes: localizedRoutes,
    base: import.meta.env.BASE_URL,
  };
}
