import type { ApiMutationOptions } from '@lychen/vue-api-query/options';
import { useMutation, useQueryClient } from '@tanstack/vue-query';
import { useEspaceApi } from '../../../client/useEspaceApi';
import { areaActivityKeys } from '../queryKeys';
import { mapToModel, type Model } from './mapToModel';
import { mapToRequest, type UpdateAreaActivityVariables } from './mapToRequest';

/**
 * Changes an area activity, `PATCH /api/area_activities/{uuid}`, and resolves with it. By default, it then
 * refetches that area activity and the lists.
 */
export function useUpdateAreaActivity(
  options: ApiMutationOptions<Model, UpdateAreaActivityVariables> = {},
) {
  const { api } = useEspaceApi();
  const queryClient = useQueryClient();

  return useMutation({
    onSuccess: (_, { id }) =>
      Promise.all([
        queryClient.invalidateQueries({ queryKey: areaActivityKeys.detail(id) }),
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
