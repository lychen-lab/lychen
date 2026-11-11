import { useMutation, useQueryClient } from '@tanstack/vue-query';
import type {
  AreaProposal,
  AreaProposalCreateInput,
} from '@lychen/typescript-espace/models/AreaProposal';
import { useEspaceApi } from '../../client/useEspaceApi';
import type { EspaceMutationOptions } from '../options';
import { mapToCreateBody, mapToModel } from './mapper';
import { areaProposalKeys } from './queryKeys';

/** Creates an area proposal, `POST /api/area_proposals`, and resolves with it. */
export function useCreateAreaProposal(
  options: EspaceMutationOptions<AreaProposal, AreaProposalCreateInput> = {},
) {
  const { api } = useEspaceApi();
  const queryClient = useQueryClient();
  const { onSuccess, ...mutationOptions } = options;

  return useMutation({
    ...mutationOptions,
    async mutationFn(input: AreaProposalCreateInput) {
      const { data } = await api.apiAreaProposalsPost({
        body: mapToCreateBody(input),
        throwOnError: true,
      });
      return mapToModel(data);
    },
    async onSuccess(proposal, input, onMutateResult, context) {
      // The response is the proposal as GET returns it: its page needs no fetch.
      queryClient.setQueryData(areaProposalKeys.detail(proposal.uuid), proposal);
      await queryClient.invalidateQueries({ queryKey: areaProposalKeys.lists() });
      await onSuccess?.(proposal, input, onMutateResult, context);
    },
  });
}
