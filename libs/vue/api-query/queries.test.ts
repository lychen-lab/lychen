import { QueryClient, VueQueryPlugin } from '@tanstack/vue-query';
import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
import { createApp, effectScope, reactive, ref, type EffectScope } from 'vue';
import { createQueryKeys } from './queryKeys';
import { useCollectionQuery } from './useCollectionQuery';
import { useDeleteMutation } from './useDeleteMutation';
import { useItemQuery } from './useItemQuery';
import { useSaveMutation } from './useSaveMutation';

interface Thing {
  id: string;
  name: string;
}

const thingKeys = createQueryKeys<{ page?: number }>('test-api', 'things');

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
  it('nests every key of a resource under the API and the resource', () => {
    expect(thingKeys.all).toEqual(['test-api', 'things']);
    expect(thingKeys.lists()).toEqual(['test-api', 'things', 'list']);
    expect(thingKeys.list({ page: 2 })).toEqual(['test-api', 'things', 'list', { page: 2 }]);
    expect(thingKeys.details()).toEqual(['test-api', 'things', 'detail']);
    expect(thingKeys.detail('a')).toEqual(['test-api', 'things', 'detail', 'a']);
  });
});

describe('useItemQuery', () => {
  it('waits for an id, then fetches again whenever it changes', async () => {
    const fetch = vi.fn(async (id: string): Promise<Thing> => ({ id, name: `Thing ${id}` }));
    const id = ref<string>();

    const { data } = run(() => useItemQuery(id, { queryKey: thingKeys.detail, fetch }));
    await tick();
    expect(fetch).not.toHaveBeenCalled();

    id.value = 'a';
    await vi.waitFor(() => expect(data.value).toEqual({ id: 'a', name: 'Thing a' }));
    id.value = 'b';
    await vi.waitFor(() => expect(data.value?.id).toBe('b'));

    expect(fetch.mock.calls).toEqual([['a'], ['b']]);
    expect(queryClient.getQueryData(thingKeys.detail('b'))).toEqual({ id: 'b', name: 'Thing b' });
  });

  it('waits for its own enabled option too', async () => {
    const fetch = vi.fn(async (id: string): Promise<Thing> => ({ id, name: '' }));
    const enabled = ref(false);

    const { data } = run(() =>
      useItemQuery('a', { queryKey: thingKeys.detail, fetch }, { enabled }),
    );
    await tick();
    expect(fetch).not.toHaveBeenCalled();

    enabled.value = true;
    await vi.waitFor(() => expect(data.value?.id).toBe('a'));
  });

  it("takes Tanstack's own options, and only those", async () => {
    const fetch = vi.fn(async (id: string): Promise<Thing> => ({ id, name: '' }));

    const { data } = run(() =>
      useItemQuery('a', { queryKey: thingKeys.detail, fetch }, { staleTime: 60_000 }),
    );
    await vi.waitFor(() => expect(data.value?.id).toBe('a'));
    expect(
      queryClient
        .getQueryCache()
        .find({ queryKey: thingKeys.detail('a') })
        ?.isStale(),
    ).toBe(false);

    // @ts-expect-error -- not an option of a query
    run(() => useItemQuery('b', { queryKey: thingKeys.detail, fetch }, { notAnOption: true }));
  });
});

describe('useCollectionQuery', () => {
  it('fetches again when a reactive filter changes deep down', async () => {
    const fetch = vi.fn(async ({ page }: { page?: number }) => [`page ${page}`]);
    const filters = reactive({ page: 1 });

    const { data } = run(() => useCollectionQuery(filters, { queryKey: thingKeys.list, fetch }));
    await vi.waitFor(() => expect(data.value).toEqual(['page 1']));
    filters.page = 2;
    await vi.waitFor(() => expect(data.value).toEqual(['page 2']));

    expect(fetch).toHaveBeenCalledTimes(2);
  });
});

describe('useSaveMutation', () => {
  it("caches the saved item and refetches the lists, before the caller's onSuccess", async () => {
    const invalidateQueries = vi.spyOn(queryClient, 'invalidateQueries');
    const cachedWhenCallerRuns: unknown[] = [];

    const { mutateAsync } = run(() =>
      useSaveMutation(
        {
          queryKeys: thingKeys,
          save: async (name: string): Promise<Thing> => ({ id: 'new', name }),
          idOf: (thing) => thing.id,
        },
        {
          onSuccess: (thing) => {
            cachedWhenCallerRuns.push(queryClient.getQueryData(thingKeys.detail(thing.id)));
          },
        },
      ),
    );
    const thing = await mutateAsync('Created');

    expect(thing).toEqual({ id: 'new', name: 'Created' });
    expect(queryClient.getQueryData(thingKeys.detail('new'))).toEqual(thing);
    expect(invalidateQueries).toHaveBeenCalledWith({ queryKey: thingKeys.lists() });
    expect(cachedWhenCallerRuns).toEqual([thing]);
  });

  it('leaves the cache alone when the request fails', async () => {
    const onSuccess = vi.fn();

    const { mutateAsync } = run(() =>
      useSaveMutation(
        {
          queryKeys: thingKeys,
          save: async (): Promise<Thing> => {
            throw new Error('422 Unprocessable Content');
          },
          idOf: (thing) => thing.id,
        },
        { onSuccess },
      ),
    );

    await expect(mutateAsync(undefined)).rejects.toThrow('422');
    expect(queryClient.getQueryCache().getAll()).toHaveLength(0);
    expect(onSuccess).not.toHaveBeenCalled();
  });
});

describe('useDeleteMutation', () => {
  it('drops the deleted item from the cache and refetches the lists', async () => {
    const remove = vi.fn(async () => undefined);
    const invalidateQueries = vi.spyOn(queryClient, 'invalidateQueries');
    queryClient.setQueryData(thingKeys.detail('a'), { id: 'a', name: 'Thing a' });

    const { mutateAsync } = run(() => useDeleteMutation({ queryKeys: thingKeys, remove }));
    await mutateAsync('a');

    expect(remove).toHaveBeenCalledWith('a');
    expect(queryClient.getQueryData(thingKeys.detail('a'))).toBeUndefined();
    expect(invalidateQueries).toHaveBeenCalledWith({ queryKey: thingKeys.lists() });
  });
});
