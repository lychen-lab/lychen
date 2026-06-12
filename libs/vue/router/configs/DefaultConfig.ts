import { RouterView, type NavigationGuardWithThis, type RouteRecordRaw } from 'vue-router';
import { scrollBehavior } from './ScrollBehavior';

export function buildConfigObject(
  routes: RouteRecordRaw[],
  beforeEnter?: NavigationGuardWithThis<undefined>,
) {
  const localizedRoutes: RouteRecordRaw[] = [
    {
      path: '/:locale?',
      component: RouterView,
      ...(beforeEnter ? { beforeEnter } : {}),
      children: routes,
    },
  ];
  return {
    scrollBehavior,
    routes: localizedRoutes,
    base: import.meta.env.BASE_URL,
  };
}
