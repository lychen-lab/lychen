import type { SeedLot } from '@lychen/typescript-flora-models/SeedLot';
import type { ApiQueryOptions } from '@lychen/vue-api-query/options';
import { useQuery, type QueryKey } from '@tanstack/vue-query';
import { computed, toValue, type MaybeRefOrGetter } from 'vue';
import { useFloraApi } from '../../../client/useFloraApi';
import { seedLotKeys } from '../queryKeys';
import { mapToModel } from './mapToModel';

/**
 * One seed lot, `GET /api/seed_lots/{uuid}`. The query waits for a uuid, and fetches again
 * whenever it changes.
 */
export function useGetSeedLot(
  uuid: MaybeRefOrGetter<string | undefined>,
  options: ApiQueryOptions<SeedLot> = {},
) {
  const { api } = useFloraApi();
  const { enabled = true, ...queryOptions } = options;

  return useQuery<SeedLot, Error, SeedLot, QueryKey>({
    queryKey: computed(() => seedLotKeys.detail(toValue(uuid) ?? '')),
    ...queryOptions,
    enabled: computed(() => Boolean(toValue(uuid)) && toValue(enabled)),
    async queryFn() {
      const current = toValue(uuid);
      if (!current) {
        throw new Error('useGetSeedLot: there is no uuid to fetch yet.');
      }
      const { data } = await api.apiSeedLotsUuidGet({
        path: { uuid: current },
        throwOnError: true,
      });
      return mapToModel(data);
    },
  });
}
