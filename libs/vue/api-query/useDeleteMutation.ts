import { useMutation, useQueryClient } from '@tanstack/vue-query';
import type { ApiMutationOptions } from './options';
import type { ResourceQueryKeys } from './queryKeys';

export interface DeleteMutation {
  /** The resource's cache keys. */
  queryKeys: Pick<ResourceQueryKeys<unknown>, 'detail' | 'lists'>;
  /** Sends the `DELETE` of the item with this id. */
  remove: (id: string) => Promise<unknown>;
}

/**
 * The mutation behind an endpoint composable that deletes an item, given its id. It drops
 * the item from the cache and refetches the lists, before the caller's `onSuccess` runs.
 */
export function useDeleteMutation(
  { queryKeys, remove }: DeleteMutation,
  options: ApiMutationOptions<void, string> = {},
) {
  const queryClient = useQueryClient();
  const { onSuccess, ...mutationOptions } = options;

  return useMutation({
    ...mutationOptions,
    async mutationFn(id: string) {
      await remove(id);
    },
    async onSuccess(result, id, onMutateResult, context) {
      queryClient.removeQueries({ queryKey: queryKeys.detail(id) });
      await queryClient.invalidateQueries({ queryKey: queryKeys.lists() });
      await onSuccess?.(result, id, onMutateResult, context);
    },
  });
}
