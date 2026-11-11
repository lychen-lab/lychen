import type { ApiAreaActivitiesGetCollectionData } from '@lychen/typescript-espace-api-sdk/generated/espace-api';
import type { Collection } from '@lychen/typescript-api-mapping/Collection';
import type { ApiQueryOptions } from '@lychen/vue-api-query/options';
import { useQuery, type QueryKey } from '@tanstack/vue-query';
import { computed, toValue, type MaybeRefOrGetter } from 'vue';
import { useEspaceApi } from '../../../client/useEspaceApi';
import { areaActivityKeys } from '../queryKeys';
import { mapToModel, type Member } from './mapToModel';

/**
 * A page of the area activities, `GET /api/area_activities`, with the API's own filters. The
 * query fetches again whenever they change.
 */
export function useGetAreaActivities(
  filters: MaybeRefOrGetter<ApiAreaActivitiesGetCollectionData['query']> = {},
  options: ApiQueryOptions<Collection<Member>> = {},
) {
  const { api } = useEspaceApi();

  return useQuery<Collection<Member>, Error, Collection<Member>, QueryKey>({
    queryKey: computed(() => areaActivityKeys.list(toValue(filters))),
    ...options,
    async queryFn() {
      const { data } = await api.apiAreaActivitiesGetCollection({
        query: toValue(filters),
        throwOnError: true,
      });
      return mapToModel(data);
    },
  });
}
