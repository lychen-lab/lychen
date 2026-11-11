import type {
  AreaRequest,
  AreaRequestCreateInput,
} from '@lychen/typescript-espace-models/AreaRequest';
import type { ApiMutationOptions } from '@lychen/vue-api-query/options';
import { useSaveMutation } from '@lychen/vue-api-query/useSaveMutation';
import { useEspaceApi } from '../../client/useEspaceApi';
import { mapToCreateBody, mapToModel } from './mapper';
import { areaRequestKeys } from './queryKeys';

/** Creates an area request, `POST /api/area_requests`, and resolves with it. */
export function useCreateAreaRequest(
  options: ApiMutationOptions<AreaRequest, AreaRequestCreateInput> = {},
) {
  const { api } = useEspaceApi();

  return useSaveMutation(
    {
      queryKeys: areaRequestKeys,
      async save(input: AreaRequestCreateInput) {
        const { data } = await api.apiAreaRequestsPost({
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
