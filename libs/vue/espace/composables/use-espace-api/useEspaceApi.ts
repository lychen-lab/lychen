import createClient, { type Middleware } from 'openapi-fetch';
import type { paths } from '@lychen/typescript-espace-api-sdk/generated/espace-api';
import zitadelAuth from '@lychen/typescript-zitadel/ZitadelAuth';

const middleware: Middleware = {
  async onRequest({ request, options }) {
    const { isAuthenticated, accessToken } = zitadelAuth.oidcAuth;
    if (isAuthenticated) {
      request.headers.set('Authorization', `Bearer ${accessToken}`);
      request.headers.set('Content-Type', 'application/ld+json');
    }

    if (request.method === 'PATCH') {
      request.headers.set('Content-Type', 'application/merge-patch+json');
    }

    return request;
  },
  async onResponse({ request, response, options }) {
    if (!response.ok) {
      // Will produce error messages like "https://example.org/api/v1/example: 404 Not Found".
      try {
        const errorData = await response.clone().json();
        throw new Error(
          `${response.url}: ${response.status} ${response.statusText} - ${JSON.stringify(errorData)}`,
        );
      } catch {
        throw new Error(`${response.url}: ${response.status} ${response.statusText}`);
      }
    }
    return response;
  },
};

const client = createClient<paths>({ baseUrl: import.meta.env.VITE_ESPACE_API_HOST });

client.use(middleware);

export function useEspaceApi() {
  return { api: client };
}
