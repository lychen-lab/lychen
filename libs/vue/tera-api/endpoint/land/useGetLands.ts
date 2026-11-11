import type { Collection } from '@lychen/typescript-api-mapping/Collection';
import { toCollection, withoutUndefined } from '@lychen/typescript-api-mapping/mapping';
import type { LandFilters, LandSummary } from '@lychen/typescript-tera-models/Land';
import type { ApiQueryOptions } from '@lychen/vue-api-query/options';
import { useCollectionQuery } from '@lychen/vue-api-query/useCollectionQuery';
import type { MaybeRefOrGetter } from 'vue';
import { useTeraApi } from '../../client/useTeraApi';
import { mapToSummaryModel } from './mapper';
import { landKeys } from './queryKeys';

/**
 * A page of the lands, `GET /api/lands`. The query fetches again whenever
 * the filters change.
 */
export function useGetLands(
  filters: MaybeRefOrGetter<LandFilters> = {},
  options: ApiQueryOptions<Collection<LandSummary>> = {},
) {
  const { api } = useTeraApi();

  return useCollectionQuery(
    filters,
    {
      queryKey: landKeys.list,
      async fetch({ page, itemsPerPage }) {
        const { data } = await api.apiLandsGetCollection({
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
