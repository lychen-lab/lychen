import type {
  AreaActivity,
  AreaActivityUpdateInput,
} from '@lychen/typescript-espace-models/AreaActivity';
import type { ApiMutationOptions } from '@lychen/vue-api-query/options';
import { useSaveMutation } from '@lychen/vue-api-query/useSaveMutation';
import { useEspaceApi } from '../../client/useEspaceApi';
import { mapToModel, mapToUpdateBody } from './mapper';
import { areaActivityKeys } from './queryKeys';

export interface UpdateAreaActivityVariables {
  uuid: string;
  /** The fields to change; those left out are kept. */
  input: AreaActivityUpdateInput;
}

/** Changes an area activity, `PATCH /api/area_activities/{uuid}`, and resolves with it. */
export function useUpdateAreaActivity(
  options: ApiMutationOptions<AreaActivity, UpdateAreaActivityVariables> = {},
) {
  const { api } = useEspaceApi();

  return useSaveMutation(
    {
      queryKeys: areaActivityKeys,
      async save({ uuid, input }: UpdateAreaActivityVariables) {
        const { data } = await api.apiAreaActivitiesUuidPatch({
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
