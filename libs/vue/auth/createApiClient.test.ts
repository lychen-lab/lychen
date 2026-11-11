import { EspaceApi } from '@lychen/typescript-espace-api-sdk/generated/espace-api';
import { createClient } from '@lychen/typescript-espace-api-sdk/generated/espace-api/client';
import { beforeEach, describe, expect, it, vi } from 'vitest';
import { createApiClient } from './createApiClient';
import { httpStatusError } from './interceptors';

// A real generated client (the espace SDK's) runs every request through a fake fetch.

const auth = vi.hoisted(() => ({ isAuthenticated: false, accessToken: '' }));
vi.mock('@lychen/typescript-zitadel/ZitadelAuth', () => ({ default: { oidcAuth: auth } }));

const BASE_URL = 'https://api.example.org';

let requests: Request[];
let nextResponse: () => Response;

async function fakeFetch(input: RequestInfo | URL): Promise<Response> {
  const request = input as Request;
  requests.push(request);
  const response = nextResponse();
  // A fetched response carries its URL; a constructed one does not.
  Object.defineProperty(response, 'url', { value: request.url });
  return response;
}

function jsonLd(body: unknown, init: ResponseInit = {}): Response {
  return new Response(JSON.stringify(body), {
    headers: { 'Content-Type': 'application/ld+json' },
    ...init,
  });
}

function addLocale(request: Request): Request {
  request.headers.set('Accept-Language', 'fr-FR');
  return request;
}

function lastRequest(): Request {
  const request = requests.at(-1);
  if (!request) {
    throw new Error('No request was sent.');
  }
  return request;
}

beforeEach(() => {
  requests = [];
  nextResponse = () => jsonLd({});
  auth.isAuthenticated = false;
  auth.accessToken = '';
});

describe('createApiClient', () => {
  it('runs the SDK class on the base URL', async () => {
    const api = new EspaceApi({
      client: createApiClient(createClient, { baseUrl: BASE_URL, config: { fetch: fakeFetch } }),
    });

    await api.apiAreaProposalsUuidGet({ path: { uuid: 'p1' } });

    expect(lastRequest().url).toBe(`${BASE_URL}/api/area_proposals/p1`);
  });

  it("sends the signed-in user's Zitadel token", async () => {
    auth.isAuthenticated = true;
    auth.accessToken = 'token-1';
    const client = createApiClient(createClient, {
      baseUrl: BASE_URL,
      config: { fetch: fakeFetch },
    });

    await client.get({ url: '/api/area_proposals' });

    expect(lastRequest().headers.get('Authorization')).toBe('Bearer token-1');
  });

  it('sends no token when nobody is signed in', async () => {
    const client = createApiClient(createClient, {
      baseUrl: BASE_URL,
      config: { fetch: fakeFetch },
    });

    await client.get({ url: '/api/area_proposals' });

    expect(lastRequest().headers.has('Authorization')).toBe(false);
  });

  it('keeps the Content-Type the SDK sets from the spec, and gives a read none', async () => {
    const api = new EspaceApi({
      client: createApiClient(createClient, { baseUrl: BASE_URL, config: { fetch: fakeFetch } }),
    });

    await api.apiAreaProposalsUuidPatch({ path: { uuid: 'p1' }, body: {} });
    expect(lastRequest().headers.get('Content-Type')).toBe('application/merge-patch+json');

    await api.apiAreaProposalsUuidGet({ path: { uuid: 'p1' } });
    expect(lastRequest().headers.has('Content-Type')).toBe(false);
  });

  it('gives a write without a body the JSON-LD Content-Type', async () => {
    const client = createApiClient(createClient, {
      baseUrl: BASE_URL,
      config: { fetch: fakeFetch },
    });

    await client.post({ url: '/api/area_proposals/p1/accept' });
    expect(lastRequest().headers.get('Content-Type')).toBe('application/ld+json');

    await client.patch({ url: '/api/area_proposals/p1' });
    expect(lastRequest().headers.get('Content-Type')).toBe('application/merge-patch+json');
  });

  it('rejects a non-2xx response with its URL and status, the body as the cause', async () => {
    nextResponse = () => jsonLd({ detail: 'Not here' }, { status: 404, statusText: 'Not Found' });
    const client = createApiClient(createClient, {
      baseUrl: BASE_URL,
      config: { fetch: fakeFetch },
    });

    const rejection = client.get({ url: '/api/area_proposals/p1' });

    await expect(rejection).rejects.toThrow(`${BASE_URL}/api/area_proposals/p1: 404 Not Found`);
    await expect(rejection).rejects.toMatchObject({ cause: { detail: 'Not here' } });
  });

  it("completes the defaults with an API's own interceptors", async () => {
    auth.isAuthenticated = true;
    auth.accessToken = 'token-1';
    const client = createApiClient(createClient, {
      baseUrl: BASE_URL,
      config: { fetch: fakeFetch },
      interceptors: (defaults) => ({ request: [...defaults.request, addLocale] }),
    });

    await client.get({ url: '/api/area_proposals' });

    expect(lastRequest().headers.get('Accept-Language')).toBe('fr-FR');
    expect(lastRequest().headers.get('Authorization')).toBe('Bearer token-1');
  });

  it('lets an API drop a default interceptor', async () => {
    nextResponse = () => jsonLd({ detail: 'Not here' }, { status: 404, statusText: 'Not Found' });
    const client = createApiClient(createClient, {
      baseUrl: BASE_URL,
      config: { fetch: fakeFetch },
      interceptors: (defaults) => ({
        error: defaults.error.filter((fn) => fn !== httpStatusError),
      }),
    });

    await expect(client.get({ url: '/api/area_proposals/p1' })).rejects.toEqual({
      detail: 'Not here',
    });
  });

  it("keeps each API's interceptors to its own client", async () => {
    createApiClient(createClient, {
      baseUrl: BASE_URL,
      config: { fetch: fakeFetch },
      interceptors: (defaults) => {
        defaults.request.push(addLocale);
        return {};
      },
    });
    const other = createApiClient(createClient, {
      baseUrl: BASE_URL,
      config: { fetch: fakeFetch },
    });

    await other.get({ url: '/api/area_proposals' });

    expect(lastRequest().headers.has('Accept-Language')).toBe(false);
  });
});
