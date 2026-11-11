import type { AreaActivity } from '@lychen/typescript-espace-models/AreaActivity';
import type { ApiQueryOptions } from '@lychen/vue-api-query/options';
import { useItemQuery } from '@lychen/vue-api-query/useItemQuery';
import type { MaybeRefOrGetter } from 'vue';
import { useEspaceApi } from '../../client/useEspaceApi';
import { mapToModel } from './mapper';
import { areaActivityKeys } from './queryKeys';

/**
 * One area activity, `GET /api/area_activities/{uuid}`. The query waits for a uuid, and fetches again
 * whenever it changes.
 */
export function useGetAreaActivity(
  uuid: MaybeRefOrGetter<string | undefined>,
  options: ApiQueryOptions<AreaActivity> = {},
) {
  const { api } = useEspaceApi();

  return useItemQuery(
    uuid,
    {
      queryKey: areaActivityKeys.detail,
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
