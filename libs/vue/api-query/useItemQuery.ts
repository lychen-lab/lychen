import { useQuery, type QueryKey } from '@tanstack/vue-query';
import { computed, toValue, type MaybeRefOrGetter } from 'vue';
import type { ApiQueryOptions } from './options';

export interface ItemQuery<TModel> {
  /** The item's cache key: its resource's `ResourceQueryKeys.detail`. */
  queryKey: (id: string) => QueryKey;
  /** Fetches the item and maps the response to the front's model. */
  fetch: (id: string) => Promise<TModel>;
}

/**
 * The query behind an endpoint composable that gets one item, e.g. `GET /api/lands/{ulid}`.
 * It waits for an id, and fetches again whenever the id changes.
 */
export function useItemQuery<TModel>(
  id: MaybeRefOrGetter<string | undefined>,
  { queryKey, fetch }: ItemQuery<TModel>,
  options: ApiQueryOptions<TModel> = {},
) {
  const { enabled = true, ...queryOptions } = options;

  return useQuery<TModel, Error, TModel, QueryKey>({
    ...queryOptions,
    queryKey: computed(() => queryKey(toValue(id) ?? '')),
    queryFn() {
      // Only a manual refetch runs it before the id is set: the query is disabled until then.
      const current = toValue(id);
      if (!current) {
        throw new Error('useItemQuery: there is no id to fetch yet.');
      }
      return fetch(current);
    },
    enabled: computed(() => Boolean(toValue(id)) && toValue(enabled)),
  });
}
