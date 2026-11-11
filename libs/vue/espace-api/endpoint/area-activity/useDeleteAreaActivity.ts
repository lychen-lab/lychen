import { useMutation, useQueryClient } from '@tanstack/vue-query';
import { useEspaceApi } from '../../client/useEspaceApi';
import type { EspaceMutationOptions } from '../options';
import { areaActivityKeys } from './queryKeys';

/** Deletes an area activity, `DELETE /api/area_activities/{uuid}`, given its uuid. */
export function useDeleteAreaActivity(options: EspaceMutationOptions<void, string> = {}) {
  const { api } = useEspaceApi();
  const queryClient = useQueryClient();
  const { onSuccess, ...mutationOptions } = options;

  return useMutation({
    ...mutationOptions,
    async mutationFn(uuid: string) {
      await api.apiAreaActivitiesUuidDelete({ path: { uuid }, throwOnError: true });
    },
    async onSuccess(result, uuid, onMutateResult, context) {
      queryClient.removeQueries({ queryKey: areaActivityKeys.detail(uuid) });
      await queryClient.invalidateQueries({ queryKey: areaActivityKeys.lists() });
      await onSuccess?.(result, uuid, onMutateResult, context);
    },
  });
}
