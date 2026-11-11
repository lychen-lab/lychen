import type { ApiMutationOptions } from '@lychen/vue-api-query/options';
import { useMutation, useQueryClient } from '@tanstack/vue-query';
import { useEspaceApi } from '../../../client/useEspaceApi';
import { areaProposalKeys } from '../queryKeys';

/**
 * Deletes an area proposal, `DELETE /api/area_proposals/{uuid}`, given its id. By default, it then drops
 * it from the cache and refetches the lists.
 */
export function useDeleteAreaProposal(options: ApiMutationOptions<void, string> = {}) {
  const { api } = useEspaceApi();
  const queryClient = useQueryClient();

  return useMutation({
    onSuccess: (_, id) => {
      // Nothing left to fetch: a refetch would only fail.
      queryClient.removeQueries({ queryKey: areaProposalKeys.detail(id) });
      return queryClient.invalidateQueries({ queryKey: areaProposalKeys.lists() });
    },
    ...options,
    async mutationFn(id: string) {
      await api.apiAreaProposalsUuidDelete({ path: { uuid: id }, throwOnError: true });
    },
  });
}
