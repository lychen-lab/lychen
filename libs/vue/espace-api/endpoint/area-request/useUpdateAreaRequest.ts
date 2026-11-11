import type {
  AreaRequest,
  AreaRequestUpdateInput,
} from '@lychen/typescript-espace-models/AreaRequest';
import type { ApiMutationOptions } from '@lychen/vue-api-query/options';
import { useSaveMutation } from '@lychen/vue-api-query/useSaveMutation';
import { useEspaceApi } from '../../client/useEspaceApi';
import { mapToModel, mapToUpdateBody } from './mapper';
import { areaRequestKeys } from './queryKeys';

export interface UpdateAreaRequestVariables {
  uuid: string;
  /** The fields to change; those left out are kept. */
  input: AreaRequestUpdateInput;
}

/** Changes an area request, `PATCH /api/area_requests/{uuid}`, and resolves with it. */
export function useUpdateAreaRequest(
  options: ApiMutationOptions<AreaRequest, UpdateAreaRequestVariables> = {},
) {
  const { api } = useEspaceApi();

  return useSaveMutation(
    {
      queryKeys: areaRequestKeys,
      async save({ uuid, input }: UpdateAreaRequestVariables) {
        const { data } = await api.apiAreaRequestsUuidPatch({
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
