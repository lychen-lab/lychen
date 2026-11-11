import { toCollection, withoutUndefined } from '@lychen/typescript-api-mapping/mapping';
import type { Collection } from '@lychen/typescript-api-mapping/Collection';
import type {
  AreaRequestFilters,
  AreaRequestSummary,
} from '@lychen/typescript-espace-models/AreaRequest';
import type { ApiQueryOptions } from '@lychen/vue-api-query/options';
import { useCollectionQuery } from '@lychen/vue-api-query/useCollectionQuery';
import type { MaybeRefOrGetter } from 'vue';
import { useEspaceApi } from '../../client/useEspaceApi';
import { mapToSummaryModel } from './mapper';
import { areaRequestKeys } from './queryKeys';

/**
 * A page of the area requests the user may see, `GET /api/area_requests`. The query fetches again whenever the
 * filters change.
 */
export function useGetAreaRequests(
  filters: MaybeRefOrGetter<AreaRequestFilters> = {},
  options: ApiQueryOptions<Collection<AreaRequestSummary>> = {},
) {
  const { api } = useEspaceApi();

  return useCollectionQuery(
    filters,
    {
      queryKey: areaRequestKeys.list,
      async fetch({ status, page, itemsPerPage }) {
        const { data } = await api.apiAreaRequestsGetCollection({
          query: withoutUndefined({ place: status, page, itemsPerPage }),
          throwOnError: true,
        });
        return toCollection(data, mapToSummaryModel);
      },
    },
    options,
  );
}
