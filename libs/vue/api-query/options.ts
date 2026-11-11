import type { MutationObserverOptions, QueryKey, UseQueryOptions } from '@tanstack/vue-query';
import type { MaybeRefOrGetter } from 'vue';

/**
 * vue-query's options object, without the refs `UseQueryOptions` also accepts for the
 * whole of it. Its properties keep vue-query's own types (each may be a ref).
 */
type QueryOptionsObject<TData> = Extract<
  UseQueryOptions<TData, Error, TData, TData, QueryKey>,
  { queryKey: unknown }
>;

/**
 * What the caller of an endpoint composable that reads may set: every Tanstack Query option
 * but `queryFn`, which holds the endpoint's URL and mapper. The composable's `queryKey` is a
 * default the caller may replace; its `enabled` is combined with what the composable waits
 * for (a uuid, say).
 */
export type ApiQueryOptions<TData> = Omit<
  QueryOptionsObject<TData>,
  'queryFn' | 'queryKey' | 'enabled'
> & {
  queryKey?: QueryOptionsObject<TData>['queryKey'];
  enabled?: MaybeRefOrGetter<boolean>;
};

/**
 * What the caller of an endpoint composable that writes may set: every Tanstack mutation
 * option but `mutationFn`, which holds the endpoint's URL and mappers. The composable's
 * callbacks, its cache upkeep in `onSuccess`, are defaults the caller may replace; to react
 * to a success and keep the upkeep, pass the callback to `mutate(variables, { onSuccess })`.
 */
export type ApiMutationOptions<TData, TVariables> = Omit<
  MutationObserverOptions<TData, Error, TVariables>,
  'mutationFn'
>;
