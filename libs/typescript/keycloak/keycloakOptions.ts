import type { KeycloakConfig, KeycloakInitOptions } from 'keycloak-js';

/** The options of @josempgon/vue-keycloak's plugin (it exports no type for them). */
export interface VueKeycloakOptions {
  config: KeycloakConfig;
  /** Merged over the plugin's defaults: standard flow, login required, no login iframe. */
  initOptions?: KeycloakInitOptions;
}

/**
 * The app's Keycloak client, from its environment. keycloak-js uses PKCE (S256) by default:
 * the client must be a public one, with the app's URL as a valid redirect URI.
 *
 *   await vueKeycloak.install(app, keycloakOptions);
 */
export const keycloakOptions: VueKeycloakOptions = {
  config: {
    url: import.meta.env.VITE_KEYCLOAK_URL,
    realm: import.meta.env.VITE_KEYCLOAK_REALM,
    clientId: import.meta.env.VITE_KEYCLOAK_CLIENT_ID,
  },
};
