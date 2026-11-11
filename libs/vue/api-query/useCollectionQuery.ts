import { useQuery, type QueryKey } from '@tanstack/vue-query';
import { computed, toValue, type MaybeRefOrGetter } from 'vue';
import type { ApiQueryOptions } from './options';

export interface CollectionQuery<TData, TFilters> {
  /** The list's cache key: its resource's `ResourceQueryKeys.list`. */
  queryKey: (filters: TFilters) => QueryKey;
  /** Fetches the page these filters select and maps it, e.g. with `toCollection`. */
  fetch: (filters: TFilters) => Promise<TData>;
}

/**
 * The query behind an endpoint composable that gets a list, e.g. `GET /api/lands`. It
 * fetches again whenever the filters change, deep changes of a reactive object included.
 */
export function useCollectionQuery<TData, TFilters>(
  filters: MaybeRefOrGetter<TFilters>,
  { queryKey, fetch }: CollectionQuery<TData, TFilters>,
  options: ApiQueryOptions<TData> = {},
) {
  return useQuery<TData, Error, TData, QueryKey>({
    ...options,
    queryKey: computed(() => queryKey(toValue(filters))),
    queryFn: () => fetch(toValue(filters)),
  });
}
