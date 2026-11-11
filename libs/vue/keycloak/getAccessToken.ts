import { getToken, useKeycloak } from '@josempgon/vue-keycloak';
import { watch } from 'vue';

const { isAuthenticated, isPending } = useKeycloak();

/** Resolves once the plugin has signed the user in, or not: app.use() doesn't wait for it. */
function signInSettled(): Promise<void> {
  if (!isPending.value) {
    return Promise.resolve();
  }
  return new Promise((resolve) => {
    watch(isPending, () => resolve(), { once: true });
  });
}

/**
 * The signed-in user's access token, or undefined when nobody is signed in: a bearer
 * token provider for createApiClient (`bearerToken: getAccessToken`).
 *
 * The token is refreshed first when it expires within 10 seconds (vue-keycloak's getToken),
 * so a request never carries an expired one.
 */
export async function getAccessToken(): Promise<string | undefined> {
  await signInSettled();
  if (!isAuthenticated.value) {
    return undefined;
  }
  try {
    return await getToken();
  } catch (error) {
    // Keycloak refused the refresh token (the session expired or was ended): nobody is
    // signed in any more.
    if (!isAuthenticated.value) {
      return undefined;
    }
    // Keycloak could not be reached: the request fails with the cause, not with a 401.
    throw error;
  }
}
