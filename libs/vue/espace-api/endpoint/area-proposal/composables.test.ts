import type { ApiAreaProposalsUuidGetResponse } from '@lychen/typescript-espace-api-sdk/generated/espace-api';
import { QueryClient, VueQueryPlugin } from '@tanstack/vue-query';
import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
import { createApp, effectScope, ref, type EffectScope } from 'vue';
import { mapToModel } from './mapper';
import { areaProposalKeys } from './queryKeys';
import { useDeleteAreaProposal } from './useDeleteAreaProposal';
import { useGetAreaProposal } from './useGetAreaProposal';
import { useGetAreaProposals } from './useGetAreaProposals';
import { useUpdateAreaProposal } from './useUpdateAreaProposal';

// The composables run on a real generated client, set up like the app's, whose requests
// go to this test's fetch instead of the network.

const fetchMock = vi.hoisted(() => ({
  requests: [] as Request[],
  respond: (request: Request): Response => new Response(request.url, { status: 500 }),
}));

vi.mock('../../client/useEspaceApi', async () => {
  const [{ EspaceApi }, { createClient }, { createApiClient }] = await Promise.all([
    import('@lychen/typescript-espace-api-sdk/generated/espace-api'),
    import('@lychen/typescript-espace-api-sdk/generated/espace-api/client'),
    import('@lychen/typescript-api-client/createApiClient'),
  ]);

  async function fakeFetch(input: RequestInfo | URL): Promise<Response> {
    const request = input as Request;
    fetchMock.requests.push(request);
    const response = fetchMock.respond(request);
    // A fetched response carries its URL; a constructed one does not.
    Object.defineProperty(response, 'url', { value: request.url });
    return response;
  }

  const api = new EspaceApi({
    client: createApiClient(createClient, {
      baseUrl: 'https://api.example.org',
      config: { fetch: fakeFetch },
    }),
  });
  return { useEspaceApi: () => ({ api }) };
});

const UUID = '0198a1b2-c3d4-7e5f-8a9b-0c1d2e3f4a5b';

function apiProposal(
  overrides: Partial<ApiAreaProposalsUuidGetResponse> = {},
): ApiAreaProposalsUuidGetResponse {
  const uuid = overrides.uuid ?? UUID;
  return {
    '@id': `/api/area_proposals/${uuid}`,
    '@type': 'AreaProposal',
    uuid,
    title: 'Terrain de culture',
    description: 'Grand terrain ensoleillé.',
    surfaceToShare: 130,
    place: 'published',
    activities: [],
    ...overrides,
  };
}

function jsonLd(body: unknown, init: ResponseInit = {}): Response {
  return new Response(JSON.stringify(body), {
    headers: { 'Content-Type': 'application/ld+json' },
    ...init,
  });
}

function lastRequest(): Request {
  const request = fetchMock.requests.at(-1);
  if (!request) {
    throw new Error('No request was sent.');
  }
  return request;
}

let queryClient: QueryClient;
let scope: EffectScope;

/** Runs a composable as a component's setup would, with the test's QueryClient. */
function run<T>(composable: () => T): T {
  const app = createApp({});
  app.use(VueQueryPlugin, { queryClient });
  return app.runWithContext(() => scope.run(composable)) as T;
}

beforeEach(() => {
  fetchMock.requests = [];
  queryClient = new QueryClient({ defaultOptions: { queries: { retry: false } } });
  scope = effectScope();
});

afterEach(() => {
  scope.stop();
  queryClient.clear();
});

describe('useGetAreaProposal', () => {
  it('waits for a uuid, then fetches again whenever it changes', async () => {
    fetchMock.respond = (request) =>
      jsonLd(apiProposal({ uuid: new URL(request.url).pathname.split('/').at(-1) }));
    const uuid = ref<string>();

    const { data } = run(() => useGetAreaProposal(uuid));
    await new Promise((resolve) => setTimeout(resolve));
    expect(fetchMock.requests).toHaveLength(0);

    uuid.value = 'first';
    await vi.waitFor(() => expect(data.value?.uuid).toBe('first'));
    uuid.value = 'second';
    await vi.waitFor(() => expect(data.value?.uuid).toBe('second'));

    expect(fetchMock.requests.map(({ url }) => new URL(url).pathname)).toEqual([
      '/api/area_proposals/first',
      '/api/area_proposals/second',
    ]);
  });

  it('gives the front model', async () => {
    fetchMock.respond = () => jsonLd(apiProposal());

    const { data } = run(() => useGetAreaProposal(UUID));

    await vi.waitFor(() => expect(data.value).toEqual(mapToModel(apiProposal())));
  });

  it('fails with the error the client names the request in', async () => {
    fetchMock.respond = () =>
      jsonLd({ title: 'Not Found' }, { status: 404, statusText: 'Not Found' });

    const { error } = run(() => useGetAreaProposal(UUID));

    await vi.waitFor(() =>
      expect(error.value?.message).toBe(
        `https://api.example.org/api/area_proposals/${UUID}: 404 Not Found`,
      ),
    );
  });
});

describe('useGetAreaProposals', () => {
  it('sends the status as the API place, and gives a collection of front models', async () => {
    fetchMock.respond = () => jsonLd({ member: [apiProposal()], totalItems: 1 });

    const { data } = run(() => useGetAreaProposals({ status: 'published' }));

    await vi.waitFor(() => expect(data.value).toBeDefined());
    expect(new URL(lastRequest().url).search).toBe('?place=published');
    expect(data.value).toEqual({
      items: [expect.objectContaining({ uuid: UUID, status: 'published' })],
      totalItems: 1,
      hasNextPage: false,
    });
  });
});

describe('useUpdateAreaProposal', () => {
  it('sends a merge patch of the fields set, under the API names', async () => {
    fetchMock.respond = () => jsonLd(apiProposal({ title: 'Nouveau titre' }));

    const { mutateAsync } = run(() => useUpdateAreaProposal());
    await mutateAsync({ uuid: UUID, input: { title: 'Nouveau titre', activityCodes: ['bees'] } });

    const request = lastRequest();
    expect(request.method).toBe('PATCH');
    expect(new URL(request.url).pathname).toBe(`/api/area_proposals/${UUID}`);
    expect(request.headers.get('Content-Type')).toBe('application/merge-patch+json');
    expect(await request.json()).toEqual({ title: 'Nouveau titre', activities: ['bees'] });
  });

  it("caches the proposal it resolves with and refetches the lists, before the caller's onSuccess", async () => {
    fetchMock.respond = () => jsonLd(apiProposal({ title: 'Nouveau titre' }));
    const invalidateQueries = vi.spyOn(queryClient, 'invalidateQueries');
    const cachedWhenCallerRuns: unknown[] = [];

    const { mutateAsync } = run(() =>
      useUpdateAreaProposal({
        onSuccess: (proposal) => {
          cachedWhenCallerRuns.push(
            queryClient.getQueryData(areaProposalKeys.detail(proposal.uuid)),
          );
        },
      }),
    );
    const proposal = await mutateAsync({ uuid: UUID, input: { title: 'Nouveau titre' } });

    expect(proposal.title).toBe('Nouveau titre');
    expect(queryClient.getQueryData(areaProposalKeys.detail(UUID))).toEqual(proposal);
    expect(invalidateQueries).toHaveBeenCalledWith({ queryKey: areaProposalKeys.lists() });
    expect(cachedWhenCallerRuns).toEqual([proposal]);
  });
});

describe('useDeleteAreaProposal', () => {
  it('deletes the proposal and drops it from the cache', async () => {
    fetchMock.respond = () => new Response(null, { status: 204 });
    queryClient.setQueryData(areaProposalKeys.detail(UUID), mapToModel(apiProposal()));

    const { mutateAsync } = run(() => useDeleteAreaProposal());
    await mutateAsync(UUID);

    expect(lastRequest().method).toBe('DELETE');
    expect(queryClient.getQueryData(areaProposalKeys.detail(UUID))).toBeUndefined();
  });
});
