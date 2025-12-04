import { RouterView, type NavigationGuardWithThis, type RouteRecordRaw } from 'vue-router';
import { scrollBehavior } from './ScrollBehavior';

export function buildConfigObject(
  routes: RouteRecordRaw[],
  i18nUtilities?: unknown,
  beforeEnter?: NavigationGuardWithThis<undefined>,
) {
  if (i18nUtilities && beforeEnter) {
    const localizedRoutes: RouteRecordRaw[] = [
      {
        path: '/:locale?',
        component: RouterView,
        beforeEnter: beforeEnter,
        children: routes,
      },
    ];
    return {
      scrollBehavior,
      routes: localizedRoutes,
      base: import.meta.env.BASE_URL,
    };
  }

  return {
    scrollBehavior,
    routes,
    base: import.meta.env.BASE_URL,
  };
}
