import type { RequestInterceptor } from './interceptors';

/**
 * Gives the token to send, or undefined to send none (nobody signed in). Whatever issues
 * it (Zitadel, Keycloak, an API key...), the interceptor only sends it.
 */
export type BearerTokenProvider = () => string | undefined | Promise<string | undefined>;

/**
 * Authenticates the request with `Authorization: Bearer <token>`, the token `getToken`
 * gives at the time of the request. Without a token, the request goes out as is.
 *
 * createApiClient adds it first, for the APIs that pass `bearerToken`.
 */
export function bearerToken(getToken: BearerTokenProvider): RequestInterceptor {
  return async (request) => {
    const token = await getToken();
    if (token) {
      request.headers.set('Authorization', `Bearer ${token}`);
    }
    return request;
  };
}
