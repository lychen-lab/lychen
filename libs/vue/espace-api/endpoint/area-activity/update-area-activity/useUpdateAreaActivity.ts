import type { AreaActivity } from '@lychen/typescript-espace-models/AreaActivity';
import type { ApiMutationOptions } from '@lychen/vue-api-query/options';
import { useMutation, useQueryClient } from '@tanstack/vue-query';
import { useEspaceApi } from '../../../client/useEspaceApi';
import { areaActivityKeys } from '../queryKeys';
import { mapToModel } from './mapToModel';
import { mapToRequest, type UpdateAreaActivityVariables } from './mapToRequest';

/**
 * Changes an area activity, `PATCH /api/area_activities/{uuid}`, and resolves with it. By default, it then
 * refetches that area activity and the lists.
 */
export function useUpdateAreaActivity(
  options: ApiMutationOptions<AreaActivity, UpdateAreaActivityVariables> = {},
) {
  const { api } = useEspaceApi();
  const queryClient = useQueryClient();

  return useMutation({
    onSuccess: (_, { uuid }) =>
      Promise.all([
        queryClient.invalidateQueries({ queryKey: areaActivityKeys.detail(uuid) }),
        queryClient.invalidateQueries({ queryKey: areaActivityKeys.lists() }),
      ]),
    ...options,
    async mutationFn(variables: UpdateAreaActivityVariables) {
      const { data } = await api.apiAreaActivitiesUuidPatch({
        ...mapToRequest(variables),
        throwOnError: true,
      });
      return mapToModel(data);
    },
  });
}
