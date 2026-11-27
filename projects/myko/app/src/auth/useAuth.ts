import { inject, type InjectionKey } from 'vue';

import type { Auth } from './Auth';

export const AUTH_KEY: InjectionKey<Auth> = Symbol('myko-auth');

export function useAuth(): Auth {
  const auth = inject(AUTH_KEY);
  if (!auth) {
    throw new Error('useAuth() needs the auth provided by main.ts (app.provide(AUTH_KEY, …))');
  }
  return auth;
}
