import { useQuery, type QueryKey } from '@tanstack/vue-query';
import { computed, toValue, type MaybeRefOrGetter } from 'vue';
import type {
  AreaActivity,
  AreaActivityFilters,
} from '@lychen/typescript-espace/models/AreaActivity';
import type { Collection } from '@lychen/typescript-espace/models/Collection';
import { useEspaceApi } from '../../client/useEspaceApi';
import { toCollection, withoutUndefined } from '../mapping';
import type { EspaceQueryOptions } from '../options';
import { mapToSummaryModel } from './mapper';
import { areaActivityKeys } from './queryKeys';

/**
 * A page of the area activities, `GET /api/area_activities`. The query fetches again
 * whenever the filters change.
 */
export function useGetAreaActivities(
  filters: MaybeRefOrGetter<AreaActivityFilters> = {},
  options: EspaceQueryOptions<Collection<AreaActivity>> = {},
) {
  const { api } = useEspaceApi();

  return useQuery<Collection<AreaActivity>, Error, Collection<AreaActivity>, QueryKey>({
    ...options,
    queryKey: computed(() => areaActivityKeys.list(toValue(filters))),
    async queryFn() {
      const { page, itemsPerPage } = toValue(filters);
      const { data } = await api.apiAreaActivitiesGetCollection({
        query: withoutUndefined({ page, itemsPerPage }),
        throwOnError: true,
      });
      return toCollection(data, mapToSummaryModel);
    },
  });
}
