import type { LandProposalStateChange } from '@lychen/typescript-tera-models/LandProposalStateChange';
import type { ApiMutationOptions } from '@lychen/vue-api-query/options';
import { useMutation, useQueryClient } from '@tanstack/vue-query';
import { useTeraApi } from '../../../client/useTeraApi';
import { landProposalKeys } from '../queryKeys';
import { mapToModel } from './mapToModel';

/**
 * Publishes a land proposal, given its ulid: the `publish` transition of its workflow
 * (`PATCH /api/land_proposals/{ulid}/publish`, no field). It resolves with where the proposal
 * stands, and by default then refetches that proposal and the lists.
 */
export function usePublishLandProposal(
  options: ApiMutationOptions<LandProposalStateChange, string> = {},
) {
  const { api } = useTeraApi();
  const queryClient = useQueryClient();

  return useMutation({
    onSuccess: (_, ulid) =>
      Promise.all([
        queryClient.invalidateQueries({ queryKey: landProposalKeys.detail(ulid) }),
        queryClient.invalidateQueries({ queryKey: landProposalKeys.lists() }),
      ]),
    ...options,
    async mutationFn(ulid: string) {
      const { data } = await api.apiLandProposalsUlidpublishPatch({
        path: { ulid },
        body: {},
        throwOnError: true,
      });
      return mapToModel(data);
    },
  });
}
