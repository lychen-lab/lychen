import type { RouteMeta, Router } from 'vue-router';

import { ROUTE_NAME } from '@/router/routeNames';

/**
 * The `meta` of a route that sends an anonymous visitor to the app's own sign-in screen, for the
 * providers that have no sign-in page of their own: the fake login, and Zitadel when it is not
 * configured (the screen then says so, instead of leaving a blank page).
 */
export const SIGN_IN_SCREEN_ROUTE_META: RouteMeta = { requiresSignInScreen: true };

/**
 * Sends an anonymous visitor of a route with SIGN_IN_SCREEN_ROUTE_META to the sign-in screen,
 * which brings them back to where they were going (`redirect`) once they are signed in.
 */
export function installSignInScreenGuard(router: Router, isAuthenticated: () => boolean): void {
  router.beforeEach((to) => {
    const isProtected = to.matched.some((record) => record.meta.requiresSignInScreen);
    if (!isProtected || isAuthenticated()) {
      return true;
    }
    return { name: ROUTE_NAME.SignIn, query: { redirect: to.fullPath } };
  });
}

/**
 * Where to go once signed in: the `redirect` the guard put in the query, only if it is a path of
 * this app (`/…`, never `//host` or another scheme), the home screen otherwise.
 */
export function getRedirectTarget(redirect: unknown): string {
  const value = Array.isArray(redirect) ? redirect[0] : redirect;
  if (typeof value !== 'string' || !value.startsWith('/') || value.startsWith('//')) {
    return '/';
  }
  if (value.includes('\\')) {
    return '/';
  }
  return value;
}
