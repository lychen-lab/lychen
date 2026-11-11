import { useQuery, type QueryKey } from '@tanstack/vue-query';
import { computed, toValue, type MaybeRefOrGetter } from 'vue';
import type { AreaActivity } from '@lychen/typescript-espace/models/AreaActivity';
import { useEspaceApi } from '../../client/useEspaceApi';
import { required } from '../mapping';
import type { EspaceQueryOptions } from '../options';
import { mapToModel } from './mapper';
import { areaActivityKeys } from './queryKeys';

/**
 * One area activity, `GET /api/area_activities/{uuid}`. The query waits for a uuid, and
 * fetches again whenever it changes.
 */
export function useGetAreaActivity(
  uuid: MaybeRefOrGetter<string | undefined>,
  options: EspaceQueryOptions<AreaActivity> = {},
) {
  const { api } = useEspaceApi();
  const { enabled = true, ...queryOptions } = options;

  return useQuery<AreaActivity, Error, AreaActivity, QueryKey>({
    ...queryOptions,
    queryKey: computed(() => areaActivityKeys.detail(toValue(uuid) ?? '')),
    async queryFn() {
      const { data } = await api.apiAreaActivitiesUuidGet({
        path: { uuid: required(toValue(uuid), 'uuid to fetch the area activity of') },
        throwOnError: true,
      });
      return mapToModel(data);
    },
    enabled: computed(() => Boolean(toValue(uuid)) && toValue(enabled)),
  });
}
