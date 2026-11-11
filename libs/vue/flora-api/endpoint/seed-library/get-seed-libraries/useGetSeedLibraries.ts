import type { ApiSeedLibrariesGetCollectionData } from '@lychen/typescript-flora-api-sdk/generated/flora-api';
import type { Collection } from '@lychen/typescript-api-mapping/Collection';
import type { SeedLibrarySummary } from '@lychen/typescript-flora-models/SeedLibrary';
import type { ApiQueryOptions } from '@lychen/vue-api-query/options';
import { useQuery, type QueryKey } from '@tanstack/vue-query';
import { computed, toValue, type MaybeRefOrGetter } from 'vue';
import { useFloraApi } from '../../../client/useFloraApi';
import { seedLibraryKeys } from '../queryKeys';
import { mapToModel } from './mapToModel';

/**
 * A page of the seed libraries, `GET /api/seed_libraries`, with the API's own filters. The
 * query fetches again whenever they change.
 */
export function useGetSeedLibraries(
  filters: MaybeRefOrGetter<ApiSeedLibrariesGetCollectionData['query']> = {},
  options: ApiQueryOptions<Collection<SeedLibrarySummary>> = {},
) {
  const { api } = useFloraApi();

  return useQuery<Collection<SeedLibrarySummary>, Error, Collection<SeedLibrarySummary>, QueryKey>({
    queryKey: computed(() => seedLibraryKeys.list(toValue(filters))),
    ...options,
    async queryFn() {
      const { data } = await api.apiSeedLibrariesGetCollection({
        query: toValue(filters),
        throwOnError: true,
      });
      return mapToModel(data);
    },
  });
}
