import type { ApiQueryOptions } from '@lychen/vue-api-query/options';
import { useQuery, type QueryKey } from '@tanstack/vue-query';
import { computed, toValue, type MaybeRefOrGetter } from 'vue';
import { useTeraApi } from '../../../client/useTeraApi';
import { landKeys } from '../queryKeys';
import { mapToModel, type Model } from './mapToModel';

/**
 * One land, `GET /api/lands/{ulid}`. The query waits for an id, and fetches again
 * whenever it changes.
 */
export function useGetLand(
  id: MaybeRefOrGetter<string | undefined>,
  options: ApiQueryOptions<Model> = {},
) {
  const { api } = useTeraApi();
  const { enabled = true, ...queryOptions } = options;

  return useQuery<Model, Error, Model, QueryKey>({
    queryKey: computed(() => landKeys.detail(toValue(id) ?? '')),
    ...queryOptions,
    enabled: computed(() => Boolean(toValue(id)) && toValue(enabled)),
    async queryFn() {
      const current = toValue(id);
      if (!current) {
        throw new Error('useGetLand: there is no id to fetch yet.');
      }
      const { data } = await api.apiLandsUlidGet({ path: { ulid: current }, throwOnError: true });
      return mapToModel(data);
    },
  });
}
