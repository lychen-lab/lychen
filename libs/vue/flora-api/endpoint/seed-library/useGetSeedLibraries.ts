import type { Collection } from '@lychen/typescript-api-mapping/Collection';
import { toCollection, withoutUndefined } from '@lychen/typescript-api-mapping/mapping';
import type {
  SeedLibraryFilters,
  SeedLibrarySummary,
} from '@lychen/typescript-flora-models/SeedLibrary';
import type { ApiQueryOptions } from '@lychen/vue-api-query/options';
import { useCollectionQuery } from '@lychen/vue-api-query/useCollectionQuery';
import type { MaybeRefOrGetter } from 'vue';
import { useFloraApi } from '../../client/useFloraApi';
import { mapToSummaryModel } from './mapper';
import { seedLibraryKeys } from './queryKeys';

/**
 * A page of the seed libraries, `GET /api/seed_libraries`. The query fetches again whenever
 * the filters change.
 */
export function useGetSeedLibraries(
  filters: MaybeRefOrGetter<SeedLibraryFilters> = {},
  options: ApiQueryOptions<Collection<SeedLibrarySummary>> = {},
) {
  const { api } = useFloraApi();

  return useCollectionQuery(
    filters,
    {
      queryKey: seedLibraryKeys.list,
      async fetch({ page, itemsPerPage }) {
        const { data } = await api.apiSeedLibrariesGetCollection({
          // The filters under the API's names, which the front's are free to differ from.
          query: withoutUndefined({ page, itemsPerPage }),
          throwOnError: true,
        });
        return toCollection(data, mapToSummaryModel);
      },
    },
    options,
  );
}
