import { computed } from 'vue';

import zitadelAuth from '@lychen/vue-zitadel/ZitadelAuth';
import { getAccessToken } from '@lychen/vue-zitadel/getAccessToken';

import type { Auth } from '../Auth';

export function createZitadelAuth(): Auth {
  const { oidcAuth } = zitadelAuth;

  const isConfigured = Boolean(
    import.meta.env.VITE_ZITADEL_ISSUER && import.meta.env.VITE_ZITADEL_CLIENT_ID,
  );

  return {
    isConfigured,
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
      }
    },
  };
}
