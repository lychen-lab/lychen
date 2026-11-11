import type {
  AreaProposal,
  AreaProposalUpdateInput,
} from '@lychen/typescript-espace-models/AreaProposal';
import type { ApiMutationOptions } from '@lychen/vue-api-query/options';
import { useSaveMutation } from '@lychen/vue-api-query/useSaveMutation';
import { useEspaceApi } from '../../client/useEspaceApi';
import { mapToModel, mapToUpdateBody } from './mapper';
import { areaProposalKeys } from './queryKeys';

export interface UpdateAreaProposalVariables {
  uuid: string;
  /** The fields to change; those left out are kept. */
  input: AreaProposalUpdateInput;
}

/** Changes an area proposal, `PATCH /api/area_proposals/{uuid}`, and resolves with it. */
export function useUpdateAreaProposal(
  options: ApiMutationOptions<AreaProposal, UpdateAreaProposalVariables> = {},
) {
  const { api } = useEspaceApi();

  return useSaveMutation(
    {
      queryKeys: areaProposalKeys,
      async save({ uuid, input }: UpdateAreaProposalVariables) {
        const { data } = await api.apiAreaProposalsUuidPatch({
          path: { uuid },
          body: mapToUpdateBody(input),
          throwOnError: true,
        });
        return mapToModel(data);
      },
      idOf: (saved) => saved.uuid,
    },
    options,
  );
}
