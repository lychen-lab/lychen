import type { ApiMutationOptions } from '@lychen/vue-api-query/options';
import { useMutation, useQueryClient } from '@tanstack/vue-query';
import { useEspaceApi } from '../../../client/useEspaceApi';
import { areaActivityKeys } from '../queryKeys';

/**
 * Deletes an area activity, `DELETE /api/area_activities/{uuid}`, given its id. By default, it then drops
 * it from the cache and refetches the lists.
 */
export function useDeleteAreaActivity(options: ApiMutationOptions<void, string> = {}) {
  const { api } = useEspaceApi();
  const queryClient = useQueryClient();

  return useMutation({
    onSuccess: (_, id) => {
      // Nothing left to fetch: a refetch would only fail.
      queryClient.removeQueries({ queryKey: areaActivityKeys.detail(id) });
      return queryClient.invalidateQueries({ queryKey: areaActivityKeys.lists() });
    },
    ...options,
    async mutationFn(id: string) {
      await api.apiAreaActivitiesUuidDelete({ path: { uuid: id }, throwOnError: true });
    },
  });
}
