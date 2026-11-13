import zitadelAuth from './ZitadelAuth';

/**
 * The signed-in user's access token, or undefined when nobody is signed in: a bearer
 * token provider for createApiClient (`bearerToken: getAccessToken`).
 */
export function getAccessToken(): string | undefined {
  const { isAuthenticated, accessToken } = zitadelAuth.oidcAuth;
  return isAuthenticated ? accessToken : undefined;
}
