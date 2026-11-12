import type { ApiMutationOptions } from '@lychen/vue-api-query/options';
import { useMutation, useQueryClient } from '@tanstack/vue-query';
import { useEspaceApi } from '../../../client/useEspaceApi';
import { areaRequestKeys } from '../queryKeys';
import { mapToModel, type Model } from './mapToModel';
import { mapToRequest, type UpdateAreaRequestVariables } from './mapToRequest';

/**
 * Changes an area request, `PATCH /api/area_requests/{uuid}`, and resolves with it. By default, it then
 * refetches that area request and the lists.
 */
export function useUpdateAreaRequest(
  options: ApiMutationOptions<Model, UpdateAreaRequestVariables> = {},
) {
  const { api } = useEspaceApi();
  const queryClient = useQueryClient();

  return useMutation({
    onSuccess: (_, { id }) =>
      Promise.all([
        queryClient.invalidateQueries({ queryKey: areaRequestKeys.detail(id) }),
        queryClient.invalidateQueries({ queryKey: areaRequestKeys.lists() }),
      ]),
    ...options,
    async mutationFn(variables: UpdateAreaRequestVariables) {
      const { data } = await api.apiAreaRequestsUuidPatch({
        ...mapToRequest(variables),
        throwOnError: true,
      });
      return mapToModel(data);
    },
  });
}
