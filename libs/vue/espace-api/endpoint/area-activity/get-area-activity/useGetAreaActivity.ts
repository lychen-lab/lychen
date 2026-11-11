import type { ApiQueryOptions } from '@lychen/vue-api-query/options';
import { useQuery, type QueryKey } from '@tanstack/vue-query';
import { computed, toValue, type MaybeRefOrGetter } from 'vue';
import { useEspaceApi } from '../../../client/useEspaceApi';
import { areaActivityKeys } from '../queryKeys';
import { mapToModel, type Model } from './mapToModel';

/**
 * One area activity, `GET /api/area_activities/{uuid}`. The query waits for an id, and fetches again
 * whenever it changes.
 */
export function useGetAreaActivity(
  id: MaybeRefOrGetter<string | undefined>,
  options: ApiQueryOptions<Model> = {},
) {
  const { api } = useEspaceApi();
  const { enabled = true, ...queryOptions } = options;

  return useQuery<Model, Error, Model, QueryKey>({
    queryKey: computed(() => areaActivityKeys.detail(toValue(id) ?? '')),
    ...queryOptions,
    enabled: computed(() => Boolean(toValue(id)) && toValue(enabled)),
    async queryFn() {
      const current = toValue(id);
      if (!current) {
        throw new Error('useGetAreaActivity: there is no id to fetch yet.');
      }
      const { data } = await api.apiAreaActivitiesUuidGet({
        path: { uuid: current },
        throwOnError: true,
      });
      return mapToModel(data);
    },
  });
}
