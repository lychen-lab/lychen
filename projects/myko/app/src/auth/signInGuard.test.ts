import { createMemoryHistory, createRouter, type RouteRecordRaw } from 'vue-router';
import { describe, expect, it } from 'vitest';

import { ROUTE_NAME } from '@/router/routeNames';

import {
  SIGN_IN_SCREEN_ROUTE_META,
  getRedirectTarget,
  installSignInScreenGuard,
} from './signInGuard';

const Empty = { render: () => null };

function createTestRouter(isAuthenticated: () => boolean) {
  const routes: RouteRecordRaw[] = [
    {
      path: '/',
      component: Empty,
      meta: SIGN_IN_SCREEN_ROUTE_META,
      children: [{ path: 'logs/:uuid', component: Empty, name: ROUTE_NAME.LogDetail }],
    },
    { path: '/sign-in', component: Empty, name: ROUTE_NAME.SignIn },
    { path: '/open', component: Empty, name: 'open' },
  ];
  const router = createRouter({ history: createMemoryHistory(), routes });
  installSignInScreenGuard(router, isAuthenticated);
  return router;
}

describe('installSignInScreenGuard', () => {
  it('sends an anonymous visitor to the sign-in screen, remembering where they were going', async () => {
    const router = createTestRouter(() => false);

    await router.push('/logs/6a1c?tab=journal');

    expect(router.currentRoute.value.name).toBe(ROUTE_NAME.SignIn);
    expect(router.currentRoute.value.query).toEqual({ redirect: '/logs/6a1c?tab=journal' });
  });

  it('lets a signed-in visitor through', async () => {
    const router = createTestRouter(() => true);

    await router.push('/logs/6a1c');

    expect(router.currentRoute.value.name).toBe(ROUTE_NAME.LogDetail);
  });

  it('follows the session: signed out later, the next navigation is stopped', async () => {
    let signedIn = true;
    const router = createTestRouter(() => signedIn);
    await router.push('/logs/6a1c');

    signedIn = false;
    await router.push('/logs/6b2d');

    expect(router.currentRoute.value.name).toBe(ROUTE_NAME.SignIn);
  });

  it('never stops the routes that are not protected', async () => {
    const router = createTestRouter(() => false);

    await router.push('/open');
    expect(router.currentRoute.value.name).toBe('open');

    await router.push('/sign-in');
    expect(router.currentRoute.value.name).toBe(ROUTE_NAME.SignIn);
  });
});

describe('getRedirectTarget', () => {
  it.each(['/', '/logs/6a1c', '/logs?state=fruiting&label=B-0', '/zones/new#top'])(
    'keeps the path of the app %s',
    (path) => {
      expect(getRedirectTarget(path)).toBe(path);
    },
  );

  it('takes the first value of a repeated query parameter', () => {
    expect(getRedirectTarget(['/logs', '/zones'])).toBe('/logs');
  });

  it.each([
    undefined,
    null,
    '',
    42,
    'logs',
    '//evil.example/logs',
    'https://evil.example',
    'javascript:alert(1)',
    '/\\evil.example',
    [],
  ])('goes home instead of %j', (value) => {
    expect(getRedirectTarget(value)).toBe('/');
  });
});
