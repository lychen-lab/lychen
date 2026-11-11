import { useMutation, useQueryClient } from '@tanstack/vue-query';
import { useEspaceApi } from '../../client/useEspaceApi';
import type { EspaceMutationOptions } from '../options';
import { areaProposalKeys } from './queryKeys';

/** Deletes an area proposal, `DELETE /api/area_proposals/{uuid}`, given its uuid. */
export function useDeleteAreaProposal(options: EspaceMutationOptions<void, string> = {}) {
  const { api } = useEspaceApi();
  const queryClient = useQueryClient();
  const { onSuccess, ...mutationOptions } = options;

  return useMutation({
    ...mutationOptions,
    async mutationFn(uuid: string) {
      await api.apiAreaProposalsUuidDelete({ path: { uuid }, throwOnError: true });
    },
    async onSuccess(result, uuid, onMutateResult, context) {
      queryClient.removeQueries({ queryKey: areaProposalKeys.detail(uuid) });
      await queryClient.invalidateQueries({ queryKey: areaProposalKeys.lists() });
      await onSuccess?.(result, uuid, onMutateResult, context);
    },
  });
}
