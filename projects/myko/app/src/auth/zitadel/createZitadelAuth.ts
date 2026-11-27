import { computed } from 'vue';

import zitadelAuth from '@lychen/vue-zitadel/ZitadelAuth';
import { getAccessToken } from '@lychen/vue-zitadel/getAccessToken';

import type { Auth } from '../Auth';
import { SIGN_IN_SCREEN_ROUTE_META, installSignInScreenGuard } from '../signInGuard';

export function createZitadelAuth(): Auth {
  const { oidcAuth } = zitadelAuth;

  const isConfigured = Boolean(
    import.meta.env.VITE_ZITADEL_ISSUER && import.meta.env.VITE_ZITADEL_CLIENT_ID,
  );

  return {
    isConfigured,
    // Configured: the identity provider's own guard (`meta.authName`, as in Espace) sends an
    // anonymous visitor to Zitadel and brings them back. Not configured, there is no Zitadel to
    // go to: the sign-in screen says so.
    protectedRouteMeta: isConfigured ? { authName: oidcAuth.authName } : SIGN_IN_SCREEN_ROUTE_META,
    isAuthenticated: computed(() => oidcAuth.isAuthenticated),
    userName: computed(() =>
      oidcAuth.isAuthenticated
        ? (oidcAuth.userProfile.name ?? oidcAuth.userProfile.preferred_username)
        : undefined,
    ),
    getAccessToken,
    async signIn() {
      await oidcAuth.signIn();
    },
    async signOut() {
      await oidcAuth.signOut();
    },
    async startup(router) {
      oidcAuth.useRouter(router);
      if (isConfigured) {
        await oidcAuth.startup();
      } else {
        installSignInScreenGuard(router, () => false);
      }
    },
  };
}
