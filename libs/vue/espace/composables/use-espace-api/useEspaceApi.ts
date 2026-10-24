import { EspaceApi } from '@lychen/typescript-espace-api-sdk/generated/espace-api';
import {
  createClient,
  createConfig,
} from '@lychen/typescript-espace-api-sdk/generated/espace-api/client';
import zitadelAuth from '@lychen/typescript-zitadel/ZitadelAuth';

// Reject on any non-2xx response, as callers (and Tanstack Query) expect.
const client = createClient(
  createConfig({ baseUrl: import.meta.env.VITE_ESPACE_API_HOST, throwOnError: true }),
);

client.interceptors.request.use((request) => {
  const { isAuthenticated, accessToken } = zitadelAuth.oidcAuth;
  if (isAuthenticated) {
    request.headers.set('Authorization', `Bearer ${accessToken}`);
    request.headers.set('Content-Type', 'application/ld+json');
  }

  if (request.method === 'PATCH') {
    request.headers.set('Content-Type', 'application/merge-patch+json');
  }

  return request;
});

client.interceptors.error.use((error, response) => {
  // Network failures have no response: rethrow them as they are.
  if (!response) {
    return error;
  }
  // Will produce error messages like "https://example.org/api/v1/example: 404 Not Found",
  // with the parsed response body as the cause.
  return new Error(`${response.url}: ${response.status} ${response.statusText}`, {
    cause: error,
  });
});

const api = new EspaceApi({ client });

export function useEspaceApi() {
  return { api };
}
