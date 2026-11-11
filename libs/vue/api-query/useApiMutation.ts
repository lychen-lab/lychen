import { useMutation, useQueryClient, type QueryClient, type QueryKey } from '@tanstack/vue-query';
import type { ApiMutationOptions } from './options';

export interface ApiMutation<TVariables, TModel> {
  /**
   * Sends the request, from the front's input, and maps the response to the front model
   * this endpoint gives (or nothing, for a deletion): its own mapping, whatever the API's.
   */
  send: (variables: TVariables) => Promise<TModel>;
  /**
   * The queries the change makes stale, refetched before the caller's `onSuccess` runs: its
   * resource's (`keys.all`), and those of every other resource it changes, as a transition
   * may.
   */
  invalidates: (model: TModel, variables: TVariables) => QueryKey[];
  /**
   * Cache writes made first, without a refetch: drop the queries of a deleted item, or set
   * one whose front model the response gives in full.
   */
  update?: (queryClient: QueryClient, model: TModel, variables: TVariables) => void;
}

/**
 * The mutation behind an endpoint composable that writes, whatever the endpoint: a creation,
 * a change, a deletion, a workflow transition (`PATCH /api/seed_lots/{uuid}/give`) or a
 * custom action. Once the request succeeds it updates the cache, then runs the caller's
 * `onSuccess`, which therefore sees fresh data.
 */
export function useApiMutation<TVariables, TModel>(
  { send, invalidates, update }: ApiMutation<TVariables, TModel>,
  options: ApiMutationOptions<TModel, TVariables> = {},
) {
  const queryClient = useQueryClient();
  const { onSuccess, ...mutationOptions } = options;

  return useMutation({
    ...mutationOptions,
    mutationFn: send,
    async onSuccess(model, variables, onMutateResult, context) {
      update?.(queryClient, model, variables);
      await Promise.all(
        invalidates(model, variables).map((queryKey) =>
          queryClient.invalidateQueries({ queryKey }),
        ),
      );
      await onSuccess?.(model, variables, onMutateResult, context);
    },
  });
}
