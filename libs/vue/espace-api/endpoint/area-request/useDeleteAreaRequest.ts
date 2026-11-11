import type { ApiMutationOptions } from '@lychen/vue-api-query/options';
import { useDeleteMutation } from '@lychen/vue-api-query/useDeleteMutation';
import { useEspaceApi } from '../../client/useEspaceApi';
import { areaRequestKeys } from './queryKeys';

/** Deletes an area request, `DELETE /api/area_requests/{uuid}`, given its uuid. */
export function useDeleteAreaRequest(options: ApiMutationOptions<void, string> = {}) {
  const { api } = useEspaceApi();

  return useDeleteMutation(
    {
      queryKeys: areaRequestKeys,
      remove: (uuid) => api.apiAreaRequestsUuidDelete({ path: { uuid }, throwOnError: true }),
    },
    options,
  );
}
