import { QueryClient, VueQueryPlugin } from '@tanstack/vue-query';
import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
import { createApp, effectScope, reactive, ref, type EffectScope } from 'vue';
import { createQueryKeys } from './queryKeys';
import { useApiMutation } from './useApiMutation';
import { useApiQuery } from './useApiQuery';

interface Thing {
  id: string;
  name: string;
}

const thingKeys = createQueryKeys('test-api', 'thing');
function getThingKey(id: string | undefined) {
  return thingKeys.of('get-thing', id);
}

let queryClient: QueryClient;
let scope: EffectScope;

/** Runs a composable as a component's setup would, with the test's QueryClient. */
function run<T>(composable: () => T): T {
  const app = createApp({});
  app.use(VueQueryPlugin, { queryClient });
  return app.runWithContext(() => scope.run(composable)) as T;
}

function tick(): Promise<void> {
  return new Promise((resolve) => setTimeout(resolve));
}

beforeEach(() => {
  queryClient = new QueryClient({ defaultOptions: { queries: { retry: false } } });
  scope = effectScope();
});

afterEach(() => {
  scope.stop();
  queryClient.clear();
});

describe('createQueryKeys', () => {
  it('nests every key of a resource under the API and the resource, then the endpoint', () => {
    expect(thingKeys.all).toEqual(['test-api', 'thing']);
    expect(thingKeys.endpoint('get-things')).toEqual(['test-api', 'thing', 'get-things']);
    expect(thingKeys.of('get-things', { page: 2 })).toEqual([
      'test-api',
      'thing',
      'get-things',
      { page: 2 },
    ]);
  });
});

describe('useApiQuery', () => {
  it('waits for its params, then fetches again whenever they change', async () => {
    const fetch = vi.fn(async (id: string): Promise<Thing> => ({ id, name: `Thing ${id}` }));
    const id = ref<string>();

    const { data } = run(() => useApiQuery(id, { queryKey: getThingKey, fetch }));
    await tick();
    expect(fetch).not.toHaveBeenCalled();

    id.value = 'a';
    await vi.waitFor(() => expect(data.value).toEqual({ id: 'a', name: 'Thing a' }));
    id.value = 'b';
    await vi.waitFor(() => expect(data.value?.id).toBe('b'));

    expect(fetch.mock.calls).toEqual([['a'], ['b']]);
    expect(queryClient.getQueryData(getThingKey('b'))).toEqual({ id: 'b', name: 'Thing b' });
  });

  it('fetches again when a reactive param changes deep down', async () => {
    const fetch = vi.fn(async ({ page }: { page: number }) => [`page ${page}`]);
    const filters = reactive({ page: 1 });

    const { data } = run(() =>
      useApiQuery(filters, { queryKey: (params) => thingKeys.of('get-things', params), fetch }),
    );
    await vi.waitFor(() => expect(data.value).toEqual(['page 1']));
    filters.page = 2;
    await vi.waitFor(() => expect(data.value).toEqual(['page 2']));

    expect(fetch).toHaveBeenCalledTimes(2);
  });

  it('waits for its own enabled option too', async () => {
    const fetch = vi.fn(async (id: string): Promise<Thing> => ({ id, name: '' }));
    const enabled = ref(false);

    const { data } = run(() => useApiQuery('a', { queryKey: getThingKey, fetch }, { enabled }));
    await tick();
    expect(fetch).not.toHaveBeenCalled();

    enabled.value = true;
    await vi.waitFor(() => expect(data.value?.id).toBe('a'));
  });

  it("takes Tanstack's own options, and only those", async () => {
    const fetch = vi.fn(async (id: string): Promise<Thing> => ({ id, name: '' }));

    const { data } = run(() =>
      useApiQuery('a', { queryKey: getThingKey, fetch }, { staleTime: 60_000 }),
    );
    await vi.waitFor(() => expect(data.value?.id).toBe('a'));
    expect(
      queryClient
        .getQueryCache()
        .find({ queryKey: getThingKey('a') })
        ?.isStale(),
    ).toBe(false);

    // @ts-expect-error -- not an option of a query
    run(() => useApiQuery('b', { queryKey: getThingKey, fetch }, { notAnOption: true }));
  });
});

describe('useApiMutation', () => {
  it("writes the cache, then refetches what it invalidates, before the caller's onSuccess", async () => {
    queryClient.setQueryData(getThingKey('a'), { id: 'a', name: 'Before' });
    const invalidateQueries = vi.spyOn(queryClient, 'invalidateQueries');
    const seenByCaller: unknown[] = [];

    const { mutateAsync } = run(() =>
      useApiMutation(
        {
          send: async (name: string): Promise<Thing> => ({ id: 'a', name }),
          // A transition may change other resources too.
          invalidates: () => [thingKeys.endpoint('get-things'), ['test-api', 'other']],
          update: (client, thing) => client.setQueryData(getThingKey(thing.id), thing),
        },
        {
          onSuccess: (thing) => {
            seenByCaller.push(queryClient.getQueryData(getThingKey(thing.id)));
            seenByCaller.push(invalidateQueries.mock.calls.length);
          },
        },
      ),
    );
    const thing = await mutateAsync('After');

    expect(thing).toEqual({ id: 'a', name: 'After' });
    expect(invalidateQueries.mock.calls).toEqual([
      [{ queryKey: ['test-api', 'thing', 'get-things'] }],
      [{ queryKey: ['test-api', 'other'] }],
    ]);
    expect(seenByCaller).toEqual([{ id: 'a', name: 'After' }, 2]);
  });

  it('leaves the cache alone when the request fails', async () => {
    const onSuccess = vi.fn();
    const invalidates = vi.fn(() => [thingKeys.all]);

    const { mutateAsync } = run(() =>
      useApiMutation(
        {
          send: async (): Promise<Thing> => {
            throw new Error('422 Unprocessable Content');
          },
          invalidates,
        },
        { onSuccess },
      ),
    );

    await expect(mutateAsync(undefined)).rejects.toThrow('422');
    expect(invalidates).not.toHaveBeenCalled();
    expect(onSuccess).not.toHaveBeenCalled();
  });
});
