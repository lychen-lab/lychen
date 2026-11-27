import { computed, ref } from 'vue';

import type { Auth } from '../Auth';

const STORAGE_KEY = 'myko-fake-auth-user';
const FAKE_USER_NAME = 'Dev Myko';

/**
 * Local development only, without any Zitadel: "signing in" stores a fake user in the browser.
 * createAuth imports it behind import.meta.env.DEV, so a production build never contains it.
 * It sends no token: an API that checks one rejects these requests, as it should.
 */
export function createFakeAuth(): Auth {
  const user = ref<string | undefined>(readUser());

  return {
    isConfigured: true,
    isAuthenticated: computed(() => user.value !== undefined),
    userName: computed(() => user.value),
    getAccessToken: () => undefined,
    async signIn() {
      user.value = FAKE_USER_NAME;
      writeUser(user.value);
    },
    async signOut() {
      user.value = undefined;
      writeUser(undefined);
    },
    async startup() {},
  };
}

function readUser(): string | undefined {
  try {
    return localStorage.getItem(STORAGE_KEY) ?? undefined;
  } catch {
    return undefined;
  }
}

function writeUser(name: string | undefined): void {
  try {
    if (name === undefined) {
      localStorage.removeItem(STORAGE_KEY);
    } else {
      localStorage.setItem(STORAGE_KEY, name);
    }
  } catch {
    // Storage unavailable (private mode): the fake session lasts until the page reloads.
  }
}
