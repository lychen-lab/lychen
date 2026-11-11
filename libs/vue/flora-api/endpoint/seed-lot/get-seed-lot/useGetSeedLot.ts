import type { ApiQueryOptions } from '@lychen/vue-api-query/options';
import { useQuery, type QueryKey } from '@tanstack/vue-query';
import { computed, toValue, type MaybeRefOrGetter } from 'vue';
import { useFloraApi } from '../../../client/useFloraApi';
import { seedLotKeys } from '../queryKeys';
import { mapToModel, type Model } from './mapToModel';

/**
 * One seed lot, `GET /api/seed_lots/{uuid}`. The query waits for an id, and fetches again
 * whenever it changes.
 */
export function useGetSeedLot(
  id: MaybeRefOrGetter<string | undefined>,
  options: ApiQueryOptions<Model> = {},
) {
  const { api } = useFloraApi();
  const { enabled = true, ...queryOptions } = options;

  return useQuery<Model, Error, Model, QueryKey>({
    queryKey: computed(() => seedLotKeys.detail(toValue(id) ?? '')),
    ...queryOptions,
    enabled: computed(() => Boolean(toValue(id)) && toValue(enabled)),
    async queryFn() {
      const current = toValue(id);
      if (!current) {
        throw new Error('useGetSeedLot: there is no id to fetch yet.');
      }
      const { data } = await api.apiSeedLotsUuidGet({
        path: { uuid: current },
        throwOnError: true,
      });
      return mapToModel(data);
    },
  });
}
