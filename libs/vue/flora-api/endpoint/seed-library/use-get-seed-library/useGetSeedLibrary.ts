import type { SeedLibrary } from '@lychen/typescript-flora-models/SeedLibrary';
import type { ApiQueryOptions } from '@lychen/vue-api-query/options';
import { useQuery, type QueryKey } from '@tanstack/vue-query';
import { computed, toValue, type MaybeRefOrGetter } from 'vue';
import { useFloraApi } from '../../../client/useFloraApi';
import { seedLibraryKeys } from '../queryKeys';
import { mapToModel } from './mapToModel';

/**
 * One seed library, `GET /api/seed_libraries/{uuid}`. The query waits for a uuid, and
 * fetches again whenever it changes.
 */
export function useGetSeedLibrary(
  uuid: MaybeRefOrGetter<string | undefined>,
  options: ApiQueryOptions<SeedLibrary> = {},
) {
  const { api } = useFloraApi();
  const { enabled = true, ...queryOptions } = options;

  return useQuery<SeedLibrary, Error, SeedLibrary, QueryKey>({
    queryKey: computed(() => seedLibraryKeys.detail(toValue(uuid) ?? '')),
    ...queryOptions,
    enabled: computed(() => Boolean(toValue(uuid)) && toValue(enabled)),
    async queryFn() {
      const current = toValue(uuid);
      if (!current) {
        throw new Error('useGetSeedLibrary: there is no uuid to fetch yet.');
      }
      const { data } = await api.apiSeedLibrariesUuidGet({
        path: { uuid: current },
        throwOnError: true,
      });
      return mapToModel(data);
    },
  });
}
