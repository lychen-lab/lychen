import type { ApiMutationOptions } from '@lychen/vue-api-query/options';
import { useMutation, useQueryClient } from '@tanstack/vue-query';
import { useEspaceApi } from '../../../client/useEspaceApi';
import { areaProposalKeys } from '../queryKeys';
import { mapToModel, type Model } from './mapToModel';
import { mapToRequest, type UpdateAreaProposalVariables } from './mapToRequest';

/**
 * Changes an area proposal, `PATCH /api/area_proposals/{uuid}`, and resolves with it. By default, it then
 * refetches that area proposal and the lists.
 */
export function useUpdateAreaProposal(
  options: ApiMutationOptions<Model, UpdateAreaProposalVariables> = {},
) {
  const { api } = useEspaceApi();
  const queryClient = useQueryClient();

  return useMutation({
    onSuccess: (_, { id }) =>
      Promise.all([
        queryClient.invalidateQueries({ queryKey: areaProposalKeys.detail(id) }),
        queryClient.invalidateQueries({ queryKey: areaProposalKeys.lists() }),
      ]),
    ...options,
    async mutationFn(variables: UpdateAreaProposalVariables) {
      const { data } = await api.apiAreaProposalsUuidPatch({
        ...mapToRequest(variables),
        throwOnError: true,
      });
      return mapToModel(data);
    },
  });
}
