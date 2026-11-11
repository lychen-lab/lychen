import { useMutation, useQueryClient } from '@tanstack/vue-query';
import type {
  AreaProposal,
  AreaProposalUpdateInput,
} from '@lychen/typescript-espace/models/AreaProposal';
import { useEspaceApi } from '../../client/useEspaceApi';
import type { EspaceMutationOptions } from '../options';
import { mapToModel, mapToUpdateBody } from './mapper';
import { areaProposalKeys } from './queryKeys';

export interface UpdateAreaProposalVariables {
  uuid: string;
  /** The fields to change; those left out are kept. */
  input: AreaProposalUpdateInput;
}

/** Changes an area proposal, `PATCH /api/area_proposals/{uuid}`, and resolves with it. */
export function useUpdateAreaProposal(
  options: EspaceMutationOptions<AreaProposal, UpdateAreaProposalVariables> = {},
) {
  const { api } = useEspaceApi();
  const queryClient = useQueryClient();
  const { onSuccess, ...mutationOptions } = options;

  return useMutation({
    ...mutationOptions,
    async mutationFn({ uuid, input }: UpdateAreaProposalVariables) {
      const { data } = await api.apiAreaProposalsUuidPatch({
        path: { uuid },
        body: mapToUpdateBody(input),
        throwOnError: true,
      });
      return mapToModel(data);
    },
    async onSuccess(proposal, variables, onMutateResult, context) {
      // The response is the proposal as GET returns it: its page needs no fetch.
      queryClient.setQueryData(areaProposalKeys.detail(proposal.uuid), proposal);
      await queryClient.invalidateQueries({ queryKey: areaProposalKeys.lists() });
      await onSuccess?.(proposal, variables, onMutateResult, context);
    },
  });
}
