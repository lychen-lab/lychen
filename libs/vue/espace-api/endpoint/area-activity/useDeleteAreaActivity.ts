import type { ApiMutationOptions } from '@lychen/vue-api-query/options';
import { useApiMutation } from '@lychen/vue-api-query/useApiMutation';
import { useEspaceApi } from '../../client/useEspaceApi';
import { areaActivityKeys } from './queryKeys';
import { getAreaActivityQueryKey } from './useGetAreaActivity';

/** Deletes an area activity, `DELETE /api/area_activities/{uuid}`, given its uuid. */
export function useDeleteAreaActivity(options: ApiMutationOptions<void, string> = {}) {
  const { api } = useEspaceApi();

  return useApiMutation(
    {
      async send(uuid: string) {
        await api.apiAreaActivitiesUuidDelete({ path: { uuid }, throwOnError: true });
      },
      // Nothing left to fetch: a refetch would only fail.
      update: (queryClient, _, uuid) =>
        queryClient.removeQueries({ queryKey: getAreaActivityQueryKey(uuid) }),
      invalidates: () => [areaActivityKeys.all],
    },
    options,
  );
}
