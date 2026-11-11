import { useQuery, type QueryKey } from '@tanstack/vue-query';
import { computed, toValue, type MaybeRefOrGetter } from 'vue';
import type { ApiQueryOptions } from './options';

export interface ApiQuery<TParams, TModel> {
  /** The endpoint's cache key for these params: `keys.of('<composable>', params)`. */
  queryKey: (params: TParams | undefined) => QueryKey;
  /**
   * Sends the request these params make and maps the response to the front model this
   * endpoint gives: its own mapping, whatever the API's representation.
   */
  fetch: (params: TParams) => Promise<TModel>;
}

/**
 * The query behind an endpoint composable that reads, whatever the endpoint: one item, a
 * page of a list, or a custom one (`GET /api/land_requests/public`). It waits while the
 * params are undefined, and fetches again whenever they change, deep changes of a reactive
 * object included. An endpoint without params passes `{}`.
 */
export function useApiQuery<TParams, TModel>(
  params: MaybeRefOrGetter<TParams | undefined>,
  { queryKey, fetch }: ApiQuery<TParams, TModel>,
  options: ApiQueryOptions<TModel> = {},
) {
  const { enabled = true, ...queryOptions } = options;

  return useQuery<TModel, Error, TModel, QueryKey>({
    ...queryOptions,
    queryKey: computed(() => queryKey(toValue(params))),
    queryFn() {
      // Only a manual refetch runs it without params: the query is disabled until then.
      const current = toValue(params);
      if (current === undefined) {
        throw new Error('useApiQuery: there are no params to fetch with yet.');
      }
      return fetch(current);
    },
    enabled: computed(() => toValue(params) !== undefined && toValue(enabled)),
  });
}
