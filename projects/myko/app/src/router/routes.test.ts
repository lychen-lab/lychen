import { computed } from 'vue';
import { createMemoryHistory, createRouter } from 'vue-router';
import { describe, expect, it } from 'vitest';

import type { Auth } from '@/auth';
import { SIGN_IN_SCREEN_ROUTE_META, installSignInScreenGuard } from '@/auth/signInGuard';
import { MESSAGES } from '@/views/provisional/i18n';
import { PROVISIONAL_ROUTES } from '@/views/provisional';

import { ROUTE_NAME } from './routeNames';
import { createRoutes } from './routes';

function createTestAuth(isSignedIn = true): Auth {
  return {
    isConfigured: true,
    isAuthenticated: computed(() => isSignedIn),
    userName: computed(() => (isSignedIn ? 'Test' : undefined)),
    protectedRouteMeta: SIGN_IN_SCREEN_ROUTE_META,
    getAccessToken: () => undefined,
    signIn: async () => {},
    signOut: async () => {},
    startup: async () => {},
  };
}

function createTestRouter(auth: Auth = createTestAuth()) {
  return createRouter({ history: createMemoryHistory(), routes: createRoutes(auth) });
}

// The paths of projects/myko/CLAUDE.md, "App conventions": the QR entry point is tested apart.
const PATHS: Record<string, string> = {
  '/': ROUTE_NAME.Home,
  '/more': ROUTE_NAME.More,
  '/sign-in': ROUTE_NAME.SignIn,
  '/logs': ROUTE_NAME.Logs,
  '/logs/new': ROUTE_NAME.LogNew,
  '/logs/new-batch': ROUTE_NAME.LogNewBatch,
  '/logs/6a1c': ROUTE_NAME.LogDetail,
  '/logs/6a1c/edit': ROUTE_NAME.LogEdit,
  '/zones': ROUTE_NAME.Zones,
  '/zones/new': ROUTE_NAME.ZoneNew,
  '/zones/6a1c/edit': ROUTE_NAME.ZoneEdit,
  '/strains': ROUTE_NAME.Strains,
  '/strains/new': ROUTE_NAME.StrainNew,
  '/strains/6a1c/edit': ROUTE_NAME.StrainEdit,
  '/labels': ROUTE_NAME.Labels,
  '/scan': ROUTE_NAME.Scan,
  '/stats': ROUTE_NAME.Stats,
};

describe('routes', () => {
  it.each(Object.entries(PATHS))('%s is the %s screen', (path, name) => {
    expect(createTestRouter().resolve(path).name).toBe(name);
  });

  it('keeps "new" and "new-batch" out of the log uuid', () => {
    const router = createTestRouter();

    expect(router.resolve('/logs/new').params).toEqual({});
    expect(router.resolve('/logs/new-batch').params).toEqual({});
    expect(router.resolve('/logs/6a1c').params).toEqual({ uuid: '6a1c' });
  });

  it('forwards a label QR code, /l/<uuid>, to the log page', async () => {
    const router = createTestRouter();

    await router.push('/l/6a1c-qr');

    expect(router.currentRoute.value.fullPath).toBe('/logs/6a1c-qr');
    expect(router.currentRoute.value.name).toBe(ROUTE_NAME.LogDetail);
  });

  it('sends an unknown path to the home screen', async () => {
    const router = createTestRouter();

    await router.push('/nowhere/at/all');

    expect(router.currentRoute.value.name).toBe(ROUTE_NAME.Home);
  });

  describe('sign-in', () => {
    it('protects every screen of the layout, and only the sign-in screen is open', () => {
      const router = createTestRouter();
      function resolve(path: string) {
        return router.resolve(path).matched.map((record) => record.meta);
      }

      for (const path of Object.keys(PATHS).filter((path) => path !== '/sign-in')) {
        expect(resolve(path).some((meta) => meta.requiresSignInScreen)).toBe(true);
      }
      expect(resolve('/sign-in').some((meta) => meta.requiresSignInScreen)).toBe(false);
    });

    it('puts what the auth asks on the protected routes', () => {
      const auth = { ...createTestAuth(), protectedRouteMeta: { authName: 'zitadel' } };

      const matched = createTestRouter(auth).resolve('/logs').matched;

      expect(matched.some((record) => record.meta.authName === 'zitadel')).toBe(true);
    });

    it('asks an anonymous visitor to sign in before a log opened from its QR code', async () => {
      const router = createTestRouter(createTestAuth(false));
      installSignInScreenGuard(router, () => false);

      await router.push('/l/6a1c-qr');

      expect(router.currentRoute.value.name).toBe(ROUTE_NAME.SignIn);
      expect(router.currentRoute.value.query).toEqual({ redirect: '/logs/6a1c-qr' });
    });

    it('asks an anonymous visitor to sign in before an unknown path, which leads home', async () => {
      const router = createTestRouter(createTestAuth(false));
      installSignInScreenGuard(router, () => false);

      await router.push('/nowhere');

      expect(router.currentRoute.value.name).toBe(ROUTE_NAME.SignIn);
      expect(router.currentRoute.value.query).toEqual({ redirect: '/' });
    });
  });

  it.each(Object.entries(MESSAGES).map(([locale, messages]) => [locale, messages.titles]))(
    'gives every provisional screen a title in %s',
    (_locale, titles) => {
      for (const route of PROVISIONAL_ROUTES) {
        expect(titles[route.name]).toBeTruthy();
      }
    },
  );
});
