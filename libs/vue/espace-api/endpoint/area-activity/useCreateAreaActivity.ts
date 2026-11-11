import type {
  AreaActivity,
  AreaActivityCreateInput,
} from '@lychen/typescript-espace-models/AreaActivity';
import type { ApiMutationOptions } from '@lychen/vue-api-query/options';
import { useSaveMutation } from '@lychen/vue-api-query/useSaveMutation';
import { useEspaceApi } from '../../client/useEspaceApi';
import { mapToCreateBody, mapToModel } from './mapper';
import { areaActivityKeys } from './queryKeys';

/** Creates an area activity, `POST /api/area_activities`, and resolves with it. */
export function useCreateAreaActivity(
  options: ApiMutationOptions<AreaActivity, AreaActivityCreateInput> = {},
) {
  const { api } = useEspaceApi();

  return useSaveMutation(
    {
      queryKeys: areaActivityKeys,
      async save(input: AreaActivityCreateInput) {
        const { data } = await api.apiAreaActivitiesPost({
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
