import type {
  ApiAreaActivitiesPostData,
  ApiAreaActivitiesPostResponse,
} from '@lychen/typescript-espace-api-sdk/generated/espace-api';
import { required, toDate, withoutUndefined } from '@lychen/typescript-api-mapping/mapping';
import type {
  AreaActivity,
  AreaActivityCreateInput,
} from '@lychen/typescript-espace-models/AreaActivity';
import type { ApiMutationOptions } from '@lychen/vue-api-query/options';
import { useApiMutation } from '@lychen/vue-api-query/useApiMutation';
import { useEspaceApi } from '../../client/useEspaceApi';
import { areaActivityKeys } from './queryKeys';

/** Creates an area activity, `POST /api/area_activities`, and resolves with it. */
export function useCreateAreaActivity(
  options: ApiMutationOptions<AreaActivity, AreaActivityCreateInput> = {},
) {
  const { api } = useEspaceApi();

  return useApiMutation(
    {
      async send(variables: AreaActivityCreateInput) {
        const { data } = await api.apiAreaActivitiesPost({
          ...mapToRequest(variables),
          throwOnError: true,
        });
        return mapToModel(data);
      },
      invalidates: () => [areaActivityKeys.all],
    },
    options,
  );
}

/** The request, from the front's input. */
export function mapToRequest(
  input: AreaActivityCreateInput,
): Omit<ApiAreaActivitiesPostData, 'url'> {
  return { body: withoutUndefined({ uuid: input.uuid, code: input.code }) };
}

/** The created activity as the front's model: the API leaves its code out. */
export function mapToModel(data: ApiAreaActivitiesPostResponse): AreaActivity {
  return {
    uuid: required(data.uuid, 'area activity uuid'),
    createdAt: toDate(data.createdAt),
  };
}
