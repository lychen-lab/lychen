import type { MutationObserverOptions, QueryKey, QueryObserverOptions } from '@tanstack/vue-query';
import type { MaybeRefOrGetter } from 'vue';

/**
 * Tanstack Query's options of a query an endpoint composable runs (staleTime, retry,
 * select…), without those it sets itself.
 */
export type EspaceQueryOptions<TData> = Omit<
  QueryObserverOptions<TData, Error, TData, TData, QueryKey>,
  'queryKey' | 'queryFn' | 'enabled'
> & {
  /** Runs the query only while this is true, on top of what the composable waits for. */
  enabled?: MaybeRefOrGetter<boolean>;
};

/**
 * Tanstack Query's options of a mutation an endpoint composable runs. `onSuccess` runs
 * once the composable has updated the cache, so it already sees the change.
 */
export type EspaceMutationOptions<TData, TVariables> = Omit<
  MutationObserverOptions<TData, Error, TVariables>,
  'mutationFn'
>;
