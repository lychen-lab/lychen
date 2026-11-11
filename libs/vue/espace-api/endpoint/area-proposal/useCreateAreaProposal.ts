import type {
  AreaProposal,
  AreaProposalCreateInput,
} from '@lychen/typescript-espace-models/AreaProposal';
import type { ApiMutationOptions } from '@lychen/vue-api-query/options';
import { useSaveMutation } from '@lychen/vue-api-query/useSaveMutation';
import { useEspaceApi } from '../../client/useEspaceApi';
import { mapToCreateBody, mapToModel } from './mapper';
import { areaProposalKeys } from './queryKeys';

/** Creates an area proposal, `POST /api/area_proposals`, and resolves with it. */
export function useCreateAreaProposal(
  options: ApiMutationOptions<AreaProposal, AreaProposalCreateInput> = {},
) {
  const { api } = useEspaceApi();

  return useSaveMutation(
    {
      queryKeys: areaProposalKeys,
      async save(input: AreaProposalCreateInput) {
        const { data } = await api.apiAreaProposalsPost({
          body: mapToCreateBody(input),
          throwOnError: true,
        });
        return mapToModel(data);
      },
      idOf: (saved) => saved.uuid,
    },
    options,
  );
}
