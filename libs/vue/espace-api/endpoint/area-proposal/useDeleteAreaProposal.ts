import type { ApiMutationOptions } from '@lychen/vue-api-query/options';
import { useApiMutation } from '@lychen/vue-api-query/useApiMutation';
import { useEspaceApi } from '../../client/useEspaceApi';
import { areaProposalKeys } from './queryKeys';
import { getAreaProposalQueryKey } from './useGetAreaProposal';

/** Deletes an area proposal, `DELETE /api/area_proposals/{uuid}`, given its uuid. */
export function useDeleteAreaProposal(options: ApiMutationOptions<void, string> = {}) {
  const { api } = useEspaceApi();

  return useApiMutation(
    {
      async send(uuid: string) {
        await api.apiAreaProposalsUuidDelete({ path: { uuid }, throwOnError: true });
      },
      // Nothing left to fetch: a refetch would only fail.
      update: (queryClient, _, uuid) =>
        queryClient.removeQueries({ queryKey: getAreaProposalQueryKey(uuid) }),
      invalidates: () => [areaProposalKeys.all],
    },
    options,
  );
}
