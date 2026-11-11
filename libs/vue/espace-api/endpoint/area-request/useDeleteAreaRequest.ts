import type { ApiMutationOptions } from '@lychen/vue-api-query/options';
import { useApiMutation } from '@lychen/vue-api-query/useApiMutation';
import { useEspaceApi } from '../../client/useEspaceApi';
import { areaRequestKeys } from './queryKeys';
import { getAreaRequestQueryKey } from './useGetAreaRequest';

/** Deletes an area request, `DELETE /api/area_requests/{uuid}`, given its uuid. */
export function useDeleteAreaRequest(options: ApiMutationOptions<void, string> = {}) {
  const { api } = useEspaceApi();

  return useApiMutation(
    {
      async send(uuid: string) {
        await api.apiAreaRequestsUuidDelete({ path: { uuid }, throwOnError: true });
      },
      // Nothing left to fetch: a refetch would only fail.
      update: (queryClient, _, uuid) =>
        queryClient.removeQueries({ queryKey: getAreaRequestQueryKey(uuid) }),
      invalidates: () => [areaRequestKeys.all],
    },
    options,
  );
}
