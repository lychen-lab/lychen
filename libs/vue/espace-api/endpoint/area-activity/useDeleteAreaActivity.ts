import type { ApiMutationOptions } from '@lychen/vue-api-query/options';
import { useDeleteMutation } from '@lychen/vue-api-query/useDeleteMutation';
import { useEspaceApi } from '../../client/useEspaceApi';
import { areaActivityKeys } from './queryKeys';

/** Deletes an area activity, `DELETE /api/area_activities/{uuid}`, given its uuid. */
export function useDeleteAreaActivity(options: ApiMutationOptions<void, string> = {}) {
  const { api } = useEspaceApi();

  return useDeleteMutation(
    {
      queryKeys: areaActivityKeys,
      remove: (uuid) => api.apiAreaActivitiesUuidDelete({ path: { uuid }, throwOnError: true }),
    },
    options,
  );
}
