import { useMutation, useQueryClient } from '@tanstack/vue-query';
import type {
  AreaRequest,
  AreaRequestUpdateInput,
} from '@lychen/typescript-espace/models/AreaRequest';
import { useEspaceApi } from '../../client/useEspaceApi';
import type { EspaceMutationOptions } from '../options';
import { mapToModel, mapToUpdateBody } from './mapper';
import { areaRequestKeys } from './queryKeys';

export interface UpdateAreaRequestVariables {
  uuid: string;
  /** The fields to change; those left out are kept. */
  input: AreaRequestUpdateInput;
}

/** Changes an area request, `PATCH /api/area_requests/{uuid}`, and resolves with it. */
export function useUpdateAreaRequest(
  options: EspaceMutationOptions<AreaRequest, UpdateAreaRequestVariables> = {},
) {
  const { api } = useEspaceApi();
  const queryClient = useQueryClient();
  const { onSuccess, ...mutationOptions } = options;

  return useMutation({
    ...mutationOptions,
    async mutationFn({ uuid, input }: UpdateAreaRequestVariables) {
      const { data } = await api.apiAreaRequestsUuidPatch({
        path: { uuid },
        body: mapToUpdateBody(input),
        throwOnError: true,
      });
      return mapToModel(data);
    },
    async onSuccess(request, variables, onMutateResult, context) {
      // The response is the request as GET returns it: its page needs no fetch.
      queryClient.setQueryData(areaRequestKeys.detail(request.uuid), request);
      await queryClient.invalidateQueries({ queryKey: areaRequestKeys.lists() });
      await onSuccess?.(request, variables, onMutateResult, context);
    },
  });
}
