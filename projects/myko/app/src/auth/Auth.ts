import type { ComputedRef } from 'vue';
import type { Router } from 'vue-router';

/**
 * What the app knows of authentication, whoever provides it: Zitadel, or the fake login of
 * local development (see createAuth).
 */
export interface Auth {
  /** True when an identity provider is configured, so that signing in can work. */
  readonly isConfigured: boolean;
  readonly isAuthenticated: ComputedRef<boolean>;
  readonly userName: ComputedRef<string | undefined>;
  /** The bearer token provider for createApiClient: undefined when nobody is signed in. */
  getAccessToken(): string | undefined;
  signIn(): Promise<void>;
  signOut(): Promise<void>;
  /** Restores the session, and handles the identity provider's callbacks on the router. */
  startup(router: Router): Promise<void>;
}
