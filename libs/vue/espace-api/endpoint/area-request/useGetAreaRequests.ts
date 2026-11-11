import { useQuery, type QueryKey } from '@tanstack/vue-query';
import { computed, toValue, type MaybeRefOrGetter } from 'vue';
import type {
  AreaRequestFilters,
  AreaRequestSummary,
} from '@lychen/typescript-espace/models/AreaRequest';
import type { Collection } from '@lychen/typescript-espace/models/Collection';
import { useEspaceApi } from '../../client/useEspaceApi';
import { toCollection, withoutUndefined } from '../mapping';
import type { EspaceQueryOptions } from '../options';
import { mapToSummaryModel } from './mapper';
import { areaRequestKeys } from './queryKeys';

/**
 * A page of the area requests the user may see, `GET /api/area_requests`. The query
 * fetches again whenever the filters change.
 */
export function useGetAreaRequests(
  filters: MaybeRefOrGetter<AreaRequestFilters> = {},
  options: EspaceQueryOptions<Collection<AreaRequestSummary>> = {},
) {
  const { api } = useEspaceApi();

  return useQuery<Collection<AreaRequestSummary>, Error, Collection<AreaRequestSummary>, QueryKey>({
    ...options,
    queryKey: computed(() => areaRequestKeys.list(toValue(filters))),
    async queryFn() {
      const { status, page, itemsPerPage } = toValue(filters);
      const { data } = await api.apiAreaRequestsGetCollection({
        query: withoutUndefined({ place: status, page, itemsPerPage }),
        throwOnError: true,
      });
      return toCollection(data, mapToSummaryModel);
    },
  });
}
