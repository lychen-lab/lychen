import type {
  AreaRequest,
  AreaRequestCreateInput,
} from '@lychen/typescript-espace-models/AreaRequest';
import type { ApiMutationOptions } from '@lychen/vue-api-query/options';
import { useMutation, useQueryClient } from '@tanstack/vue-query';
import { useEspaceApi } from '../../../client/useEspaceApi';
import { areaRequestKeys } from '../queryKeys';
import { mapToModel } from './mapToModel';
import { mapToRequest } from './mapToRequest';

/**
 * Creates an area request, `POST /api/area_requests`, and resolves with it. By default, it then refetches the
 * lists.
 */
export function useCreateAreaRequest(
  options: ApiMutationOptions<AreaRequest, AreaRequestCreateInput> = {},
) {
  const { api } = useEspaceApi();
  const queryClient = useQueryClient();

  return useMutation({
    onSuccess: () => queryClient.invalidateQueries({ queryKey: areaRequestKeys.lists() }),
    ...options,
    async mutationFn(input: AreaRequestCreateInput) {
      const { data } = await api.apiAreaRequestsPost({
        ...mapToRequest(input),
        throwOnError: true,
      });
      return mapToModel(data);
    },
  });
}
