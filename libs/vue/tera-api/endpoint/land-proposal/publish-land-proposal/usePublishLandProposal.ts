import type { ApiMutationOptions } from '@lychen/vue-api-query/options';
import { useMutation, useQueryClient } from '@tanstack/vue-query';
import { useTeraApi } from '../../../client/useTeraApi';
import { landProposalKeys } from '../queryKeys';
import { mapToModel, type Model } from './mapToModel';

/**
 * Publishes a land proposal, given its id: the `publish` transition of its workflow
 * (`PATCH /api/land_proposals/{ulid}/publish`, no field). It resolves with where the proposal
 * stands, and by default then refetches that proposal and the lists.
 */
export function usePublishLandProposal(options: ApiMutationOptions<Model, string> = {}) {
  const { api } = useTeraApi();
  const queryClient = useQueryClient();

  return useMutation({
    onSuccess: (_, id) =>
      Promise.all([
        queryClient.invalidateQueries({ queryKey: landProposalKeys.detail(id) }),
        queryClient.invalidateQueries({ queryKey: landProposalKeys.lists() }),
      ]),
    ...options,
    async mutationFn(id: string) {
      const { data } = await api.apiLandProposalsUlidpublishPatch({
        path: { ulid: id },
        body: {},
        throwOnError: true,
      });
      return mapToModel(data);
    },
  });
}
