import type { ApiAreaActivitiesUuidGetResponse } from '@lychen/typescript-espace-api-sdk/generated/espace-api';
import { required, toDate } from '@lychen/typescript-api-mapping/mapping';
import type { AreaActivity } from '@lychen/typescript-espace-models/AreaActivity';
import type { ApiQueryOptions } from '@lychen/vue-api-query/options';
import { useApiQuery } from '@lychen/vue-api-query/useApiQuery';
import type { MaybeRefOrGetter } from 'vue';
import { useEspaceApi } from '../../client/useEspaceApi';
import { areaActivityKeys } from './queryKeys';

/** The cache key of `useGetAreaActivity`. */
export function getAreaActivityQueryKey(uuid: string | undefined) {
  return areaActivityKeys.of('get-area-activity', uuid);
}

/**
 * One area activity, `GET /api/area_activities/{uuid}`. The query waits for a uuid, and fetches again
 * whenever it changes.
 */
export function useGetAreaActivity(
  uuid: MaybeRefOrGetter<string | undefined>,
  options: ApiQueryOptions<AreaActivity> = {},
) {
  const { api } = useEspaceApi();

  return useApiQuery(
    uuid,
    {
      queryKey: getAreaActivityQueryKey,
      async fetch(id) {
        const { data } = await api.apiAreaActivitiesUuidGet({
          path: { uuid: id },
          throwOnError: true,
        });
        return mapToModel(data);
      },
    },
    options,
  );
}

/** What this endpoint returns, as the front's model. */
export function mapToModel(data: ApiAreaActivitiesUuidGetResponse): AreaActivity {
  return {
    uuid: required(data.uuid, 'area activity uuid'),
    // The API leaves the code out of a single activity.
    createdAt: toDate(data.createdAt),
  };
}
