import type { ComputedRef } from 'vue';
import type { RouteMeta, Router } from 'vue-router';

/**
 * What the app knows of authentication, whoever provides it: Zitadel, or the fake login of
 * local development (see createAuth).
 */
export interface Auth {
  /** True when an identity provider is configured, so that signing in can work. */
  readonly isConfigured: boolean;
  readonly isAuthenticated: ComputedRef<boolean>;
  readonly userName: ComputedRef<string | undefined>;
  /**
   * The `meta` of a route that needs a signed-in user (the layout route, so every screen under
   * it): each provider guards it its own way, installed by `startup`. An anonymous visitor ends
   * up signing in, either at the identity provider or on the app's own sign-in screen.
   */
  readonly protectedRouteMeta: RouteMeta;
  /** The bearer token provider for createApiClient: undefined when nobody is signed in. */
  getAccessToken(): string | undefined;
  signIn(): Promise<void>;
  signOut(): Promise<void>;
  /**
   * Restores the session, handles the identity provider's callbacks on the router and installs
   * the guard of `protectedRouteMeta`.
   */
  startup(router: Router): Promise<void>;
}
