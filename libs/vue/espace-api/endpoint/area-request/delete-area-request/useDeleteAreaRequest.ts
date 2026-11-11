import type { ApiMutationOptions } from '@lychen/vue-api-query/options';
import { useMutation, useQueryClient } from '@tanstack/vue-query';
import { useEspaceApi } from '../../../client/useEspaceApi';
import { areaRequestKeys } from '../queryKeys';

/**
 * Deletes an area request, `DELETE /api/area_requests/{uuid}`, given its uuid. By default, it then drops
 * it from the cache and refetches the lists.
 */
export function useDeleteAreaRequest(options: ApiMutationOptions<void, string> = {}) {
  const { api } = useEspaceApi();
  const queryClient = useQueryClient();

  return useMutation({
    onSuccess: (_, uuid) => {
      // Nothing left to fetch: a refetch would only fail.
      queryClient.removeQueries({ queryKey: areaRequestKeys.detail(uuid) });
      return queryClient.invalidateQueries({ queryKey: areaRequestKeys.lists() });
    },
    ...options,
    async mutationFn(uuid: string) {
      await api.apiAreaRequestsUuidDelete({ path: { uuid }, throwOnError: true });
    },
  });
}
