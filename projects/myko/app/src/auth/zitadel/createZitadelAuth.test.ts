import { createMemoryHistory, createRouter } from 'vue-router';
import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';

import { ROUTE_NAME } from '@/router/routeNames';

const oidcAuth = {
  authName: 'zitadel-test',
  isAuthenticated: false,
  userProfile: {},
  useRouter: vi.fn(),
  startup: vi.fn(async () => true),
  signIn: vi.fn(),
  signOut: vi.fn(),
};

// Zitadel itself is not under test, and its library needs a browser.
vi.mock('@lychen/vue-zitadel/ZitadelAuth', () => ({ default: { oidcAuth } }));
vi.mock('@lychen/vue-zitadel/getAccessToken', () => ({ getAccessToken: () => undefined }));

async function load() {
  const { createZitadelAuth } = await import('./createZitadelAuth');
  return createZitadelAuth();
}

describe('createZitadelAuth', () => {
  beforeEach(() => {
    vi.stubEnv('VITE_ZITADEL_ISSUER', 'https://zitadel.example');
    vi.stubEnv('VITE_ZITADEL_CLIENT_ID', 'client-1');
  });

  afterEach(() => {
    vi.unstubAllEnvs();
    vi.clearAllMocks();
  });

  describe('configured', () => {
    it('leaves the protection to the identity provider, as Espace does', async () => {
      const auth = await load();
      const router = createRouter({ history: createMemoryHistory(), routes: [] });

      await auth.startup(router);

      expect(auth.isConfigured).toBe(true);
      expect(auth.protectedRouteMeta).toEqual({ authName: 'zitadel-test' });
      expect(oidcAuth.useRouter).toHaveBeenCalledWith(router);
      expect(oidcAuth.startup).toHaveBeenCalledOnce();
    });
  });

  describe('not configured', () => {
    beforeEach(() => {
      vi.stubEnv('VITE_ZITADEL_ISSUER', '');
    });

    it('never starts the identity provider, and asks for the sign-in screen instead', async () => {
      const Empty = { render: () => null };
      const auth = await load();
      const router = createRouter({
        history: createMemoryHistory(),
        routes: [
          { path: '/', component: Empty, meta: auth.protectedRouteMeta, name: ROUTE_NAME.Home },
          { path: '/sign-in', component: Empty, name: ROUTE_NAME.SignIn },
        ],
      });

      await auth.startup(router);
      await router.push('/');

      expect(auth.isConfigured).toBe(false);
      expect(oidcAuth.startup).not.toHaveBeenCalled();
      expect(router.currentRoute.value.name).toBe(ROUTE_NAME.SignIn);
    });
  });
});
