import type { AreaRequest } from '@lychen/typescript-espace-models/AreaRequest';
import type { ApiMutationOptions } from '@lychen/vue-api-query/options';
import { useMutation, useQueryClient } from '@tanstack/vue-query';
import { useEspaceApi } from '../../../client/useEspaceApi';
import { areaRequestKeys } from '../queryKeys';
import { mapToModel } from './mapToModel';
import { mapToRequest, type UpdateAreaRequestVariables } from './mapToRequest';

/**
 * Changes an area request, `PATCH /api/area_requests/{uuid}`, and resolves with it. By default, it then
 * refetches that area request and the lists.
 */
export function useUpdateAreaRequest(
  options: ApiMutationOptions<AreaRequest, UpdateAreaRequestVariables> = {},
) {
  const { api } = useEspaceApi();
  const queryClient = useQueryClient();

  return useMutation({
    onSuccess: (_, { uuid }) =>
      Promise.all([
        queryClient.invalidateQueries({ queryKey: areaRequestKeys.detail(uuid) }),
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
