import type { MutationObserverOptions, QueryKey, UseQueryOptions } from '@tanstack/vue-query';
import type { MaybeRefOrGetter } from 'vue';

/**
 * vue-query's options object, without the refs `UseQueryOptions` also accepts for the
 * whole of it. Its properties keep vue-query's own types (each may be a ref), which is
 * what lets a generic helper pass them on to `useQuery` unchanged.
 */
type QueryOptionsObject<TData> = Extract<
  UseQueryOptions<TData, Error, TData, TData, QueryKey>,
  { queryKey: unknown }
>;

/**
 * Tanstack Query's options of a query an endpoint composable runs (staleTime, retry,
 * select…), without those it sets itself.
 */
export type ApiQueryOptions<TData> = Omit<
  QueryOptionsObject<TData>,
  'queryKey' | 'queryFn' | 'enabled'
> & {
  /** Runs the query only while this is true, on top of what the composable waits for. */
  enabled?: MaybeRefOrGetter<boolean>;
};

/**
 * Tanstack Query's options of a mutation an endpoint composable runs. `onSuccess` runs
 * once the composable has updated the cache, so it already sees the change.
 */
export type ApiMutationOptions<TData, TVariables> = Omit<
  MutationObserverOptions<TData, Error, TVariables>,
  'mutationFn'
>;
