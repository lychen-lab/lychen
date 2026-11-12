import type { ApiMutationOptions } from '@lychen/vue-api-query/options';
import { useMutation, useQueryClient } from '@tanstack/vue-query';
import { useEspaceApi } from '../../../client/useEspaceApi';
import { areaRequestKeys } from '../queryKeys';

/**
 * Deletes an area request, `DELETE /api/area_requests/{uuid}`, given its id. By default, it then drops
 * it from the cache and refetches the lists.
 */
export function useDeleteAreaRequest(options: ApiMutationOptions<void, string> = {}) {
  const { api } = useEspaceApi();
  const queryClient = useQueryClient();

  return useMutation({
    onSuccess: (_, id) => {
      // Nothing left to fetch: a refetch would only fail.
      queryClient.removeQueries({ queryKey: areaRequestKeys.detail(id) });
      return queryClient.invalidateQueries({ queryKey: areaRequestKeys.lists() });
    },
    ...options,
    async mutationFn(id: string) {
      await api.apiAreaRequestsUuidDelete({ path: { uuid: id }, throwOnError: true });
    },
  });
}
