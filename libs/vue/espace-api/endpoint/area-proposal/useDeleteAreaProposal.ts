import type { ApiMutationOptions } from '@lychen/vue-api-query/options';
import { useDeleteMutation } from '@lychen/vue-api-query/useDeleteMutation';
import { useEspaceApi } from '../../client/useEspaceApi';
import { areaProposalKeys } from './queryKeys';

/** Deletes an area proposal, `DELETE /api/area_proposals/{uuid}`, given its uuid. */
export function useDeleteAreaProposal(options: ApiMutationOptions<void, string> = {}) {
  const { api } = useEspaceApi();

  return useDeleteMutation(
    {
      queryKeys: areaProposalKeys,
      remove: (uuid) => api.apiAreaProposalsUuidDelete({ path: { uuid }, throwOnError: true }),
    },
    options,
  );
}
