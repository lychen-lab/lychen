import type { AreaProposalCreateInput } from '@lychen/typescript-front-model-business/area/AreaProposal';
import type { ApiMutationOptions } from '@lychen/vue-api-query/options';
import { useMutation, useQueryClient } from '@tanstack/vue-query';
import { useEspaceApi } from '../../../client/useEspaceApi';
import { areaProposalKeys } from '../queryKeys';
import { mapToModel, type Model } from './mapToModel';
import { mapToRequest } from './mapToRequest';

/**
 * Creates an area proposal, `POST /api/area_proposals`, and resolves with it. By default, it then refetches the
 * lists.
 */
export function useCreateAreaProposal(
  options: ApiMutationOptions<Model, AreaProposalCreateInput> = {},
) {
  const { api } = useEspaceApi();
  const queryClient = useQueryClient();

  return useMutation({
    onSuccess: () => queryClient.invalidateQueries({ queryKey: areaProposalKeys.lists() }),
    ...options,
    async mutationFn(input: AreaProposalCreateInput) {
      const { data } = await api.apiAreaProposalsPost({
        ...mapToRequest(input),
        throwOnError: true,
      });
      return mapToModel(data);
    },
  });
}
