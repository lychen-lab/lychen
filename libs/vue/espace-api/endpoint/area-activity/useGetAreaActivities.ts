import { toCollection, withoutUndefined } from '@lychen/typescript-api-mapping/mapping';
import type { Collection } from '@lychen/typescript-api-mapping/Collection';
import type {
  AreaActivity,
  AreaActivityFilters,
} from '@lychen/typescript-espace-models/AreaActivity';
import type { ApiQueryOptions } from '@lychen/vue-api-query/options';
import { useCollectionQuery } from '@lychen/vue-api-query/useCollectionQuery';
import type { MaybeRefOrGetter } from 'vue';
import { useEspaceApi } from '../../client/useEspaceApi';
import { mapToSummaryModel } from './mapper';
import { areaActivityKeys } from './queryKeys';

/**
 * A page of the area activities, `GET /api/area_activities`. The query fetches again whenever the
 * filters change.
 */
export function useGetAreaActivities(
  filters: MaybeRefOrGetter<AreaActivityFilters> = {},
  options: ApiQueryOptions<Collection<AreaActivity>> = {},
) {
  const { api } = useEspaceApi();

  return useCollectionQuery(
    filters,
    {
      queryKey: areaActivityKeys.list,
      async fetch({ page, itemsPerPage }) {
        const { data } = await api.apiAreaActivitiesGetCollection({
          query: withoutUndefined({ page, itemsPerPage }),
          throwOnError: true,
        });
        return toCollection(data, mapToSummaryModel);
      },
    },
    options,
  );
}
