import { useMutation, useQueryClient } from '@tanstack/vue-query';
import type {
  AreaRequest,
  AreaRequestCreateInput,
} from '@lychen/typescript-espace/models/AreaRequest';
import { useEspaceApi } from '../../client/useEspaceApi';
import type { EspaceMutationOptions } from '../options';
import { mapToCreateBody, mapToModel } from './mapper';
import { areaRequestKeys } from './queryKeys';

/** Creates an area request, `POST /api/area_requests`, and resolves with it. */
export function useCreateAreaRequest(
  options: EspaceMutationOptions<AreaRequest, AreaRequestCreateInput> = {},
) {
  const { api } = useEspaceApi();
  const queryClient = useQueryClient();
  const { onSuccess, ...mutationOptions } = options;

  return useMutation({
    ...mutationOptions,
    async mutationFn(input: AreaRequestCreateInput) {
      const { data } = await api.apiAreaRequestsPost({
        body: mapToCreateBody(input),
        throwOnError: true,
      });
      return mapToModel(data);
    },
    async onSuccess(request, input, onMutateResult, context) {
      // The response is the request as GET returns it: its page needs no fetch.
      queryClient.setQueryData(areaRequestKeys.detail(request.uuid), request);
      await queryClient.invalidateQueries({ queryKey: areaRequestKeys.lists() });
      await onSuccess?.(request, input, onMutateResult, context);
    },
  });
}
