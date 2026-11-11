import { useMutation, useQueryClient } from '@tanstack/vue-query';
import type { ApiMutationOptions } from './options';
import type { ResourceQueryKeys } from './queryKeys';

export interface SaveMutation<TModel, TVariables> {
  /** The resource's cache keys. */
  queryKeys: Pick<ResourceQueryKeys<unknown>, 'detail' | 'lists'>;
  /**
   * Sends the request, from the front's input, and maps its response to the front's model.
   * The response must be the item as `GET` returns it, as API Platform's are.
   */
  save: (variables: TVariables) => Promise<TModel>;
  /** The id of the saved item, to cache it under. */
  idOf: (model: TModel) => string;
}

/**
 * The mutation behind an endpoint composable that creates or changes an item (`POST`,
 * `PATCH`, or a workflow transition). It caches the item it resolves with, so the item's
 * page needs no fetch, and refetches the lists, before the caller's `onSuccess` runs.
 */
export function useSaveMutation<TModel, TVariables>(
  { queryKeys, save, idOf }: SaveMutation<TModel, TVariables>,
  options: ApiMutationOptions<TModel, TVariables> = {},
) {
  const queryClient = useQueryClient();
  const { onSuccess, ...mutationOptions } = options;

  return useMutation({
    ...mutationOptions,
    mutationFn: save,
    async onSuccess(model, variables, onMutateResult, context) {
      queryClient.setQueryData(queryKeys.detail(idOf(model)), model);
      await queryClient.invalidateQueries({ queryKey: queryKeys.lists() });
      await onSuccess?.(model, variables, onMutateResult, context);
    },
  });
}
