import type {
  ApiAreaActivitiesUuidPatchData,
  ApiAreaActivitiesUuidPatchResponse,
} from '@lychen/typescript-espace-api-sdk/generated/espace-api';
import { required, toDate, withoutUndefined } from '@lychen/typescript-api-mapping/mapping';
import type {
  AreaActivity,
  AreaActivityUpdateInput,
} from '@lychen/typescript-espace-models/AreaActivity';
import type { ApiMutationOptions } from '@lychen/vue-api-query/options';
import { useApiMutation } from '@lychen/vue-api-query/useApiMutation';
import { useEspaceApi } from '../../client/useEspaceApi';
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

  return useApiMutation(
    {
      async send(variables: UpdateAreaActivityVariables) {
        const { data } = await api.apiAreaActivitiesUuidPatch({
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
export function mapToRequest({
  uuid,
  input,
}: UpdateAreaActivityVariables): Omit<ApiAreaActivitiesUuidPatchData, 'url'> {
  return { path: { uuid }, body: withoutUndefined({ code: input.code }) };
}

/** The changed activity as the front's model: the API leaves its code out. */
export function mapToModel(data: ApiAreaActivitiesUuidPatchResponse): AreaActivity {
  return {
    uuid: required(data.uuid, 'area activity uuid'),
    createdAt: toDate(data.createdAt),
  };
}
