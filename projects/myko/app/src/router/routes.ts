import type { RouteRecordRaw } from 'vue-router';

import type { Auth } from '@/auth';
import { ROUTE_HOME } from '@/views/home';
import { ROUTE_MORE } from '@/views/more';
import { ROUTES_PROVISIONAL } from '@/views/provisional';
import { ROUTE_SIGN_IN } from '@/views/sign-in';

import { ROUTE_NAME } from './routeNames';

/**
 * Every screen needs a signed-in user, except the sign-in screen itself. How an anonymous visitor
 * is stopped depends on the auth in use (`Auth.protectedRouteMeta`), hence a function.
 */
export function createRoutes(auth: Auth): RouteRecordRaw[] {
  return [
    {
      path: '/',
      component: () => import('@/layouts/main/LayoutMain.vue'),
      meta: auth.protectedRouteMeta,
      children: [ROUTE_HOME, ROUTE_MORE, ...ROUTES_PROVISIONAL],
    },
    ROUTE_SIGN_IN,
    {
      // What a printed label's QR code opens: `<app origin>/l/<log uuid>`. This path must never
      // change, labels are printed (and later engraved): it only forwards to the log's page,
      // which asks to sign in first if need be.
      path: '/l/:uuid',
      name: ROUTE_NAME.LogQrEntry,
      redirect: (to) => ({ name: ROUTE_NAME.LogDetail, params: { uuid: to.params.uuid } }),
    },
    {
      path: '/:pathMatch(.*)*',
      // `params: {}`: a redirect to a named route keeps the params of the path it leaves, here
      // `pathMatch`, which Home does not have (Vue Router warns and drops it).
      redirect: { name: ROUTE_NAME.Home, params: {} },
    },
  ];
}
