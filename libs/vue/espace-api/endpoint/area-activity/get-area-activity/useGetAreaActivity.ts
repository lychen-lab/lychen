import type { AreaActivity } from '@lychen/typescript-espace-models/AreaActivity';
import type { ApiQueryOptions } from '@lychen/vue-api-query/options';
import { useQuery, type QueryKey } from '@tanstack/vue-query';
import { computed, toValue, type MaybeRefOrGetter } from 'vue';
import { useEspaceApi } from '../../../client/useEspaceApi';
import { areaActivityKeys } from '../queryKeys';
import { mapToModel } from './mapToModel';

/**
 * One area activity, `GET /api/area_activities/{uuid}`. The query waits for a uuid, and fetches again
 * whenever it changes.
 */
export function useGetAreaActivity(
  uuid: MaybeRefOrGetter<string | undefined>,
  options: ApiQueryOptions<AreaActivity> = {},
) {
  const { api } = useEspaceApi();
  const { enabled = true, ...queryOptions } = options;

  return useQuery<AreaActivity, Error, AreaActivity, QueryKey>({
    queryKey: computed(() => areaActivityKeys.detail(toValue(uuid) ?? '')),
    ...queryOptions,
    enabled: computed(() => Boolean(toValue(uuid)) && toValue(enabled)),
    async queryFn() {
      const current = toValue(uuid);
      if (!current) {
        throw new Error('useGetAreaActivity: there is no uuid to fetch yet.');
      }
      const { data } = await api.apiAreaActivitiesUuidGet({
        path: { uuid: current },
        throwOnError: true,
      });
      return mapToModel(data);
    },
  });
}
