import { useMutation, useQueryClient } from '@tanstack/vue-query';
import { useEspaceApi } from '../../client/useEspaceApi';
import type { EspaceMutationOptions } from '../options';
import { areaRequestKeys } from './queryKeys';

/** Deletes an area request, `DELETE /api/area_requests/{uuid}`, given its uuid. */
export function useDeleteAreaRequest(options: EspaceMutationOptions<void, string> = {}) {
  const { api } = useEspaceApi();
  const queryClient = useQueryClient();
  const { onSuccess, ...mutationOptions } = options;

  return useMutation({
    ...mutationOptions,
    async mutationFn(uuid: string) {
      await api.apiAreaRequestsUuidDelete({ path: { uuid }, throwOnError: true });
    },
    async onSuccess(result, uuid, onMutateResult, context) {
      queryClient.removeQueries({ queryKey: areaRequestKeys.detail(uuid) });
      await queryClient.invalidateQueries({ queryKey: areaRequestKeys.lists() });
      await onSuccess?.(result, uuid, onMutateResult, context);
    },
  });
}
