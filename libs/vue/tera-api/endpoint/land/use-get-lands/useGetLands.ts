import type { ApiLandsGetCollectionData } from '@lychen/typescript-tera-api-sdk/generated/tera-api';
import type { Collection } from '@lychen/typescript-api-mapping/Collection';
import type { LandSummary } from '@lychen/typescript-tera-models/Land';
import type { ApiQueryOptions } from '@lychen/vue-api-query/options';
import { useQuery, type QueryKey } from '@tanstack/vue-query';
import { computed, toValue, type MaybeRefOrGetter } from 'vue';
import { useTeraApi } from '../../../client/useTeraApi';
import { landKeys } from '../queryKeys';
import { mapToModel } from './mapToModel';

/**
 * A page of the user's lands, `GET /api/lands`, with the API's own filters. The query
 * fetches again whenever they change.
 */
export function useGetLands(
  filters: MaybeRefOrGetter<ApiLandsGetCollectionData['query']> = {},
  options: ApiQueryOptions<Collection<LandSummary>> = {},
) {
  const { api } = useTeraApi();

  return useQuery<Collection<LandSummary>, Error, Collection<LandSummary>, QueryKey>({
    queryKey: computed(() => landKeys.list(toValue(filters))),
    ...options,
    async queryFn() {
      const { data } = await api.apiLandsGetCollection({
        query: toValue(filters),
        throwOnError: true,
      });
      return mapToModel(data);
    },
  });
}
