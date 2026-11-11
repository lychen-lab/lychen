import type { ApiLandsGetCollectionData } from '@lychen/typescript-tera-api-sdk/generated/tera-api';
import type { Collection } from '@lychen/typescript-api-mapping/Collection';
import type { ApiQueryOptions } from '@lychen/vue-api-query/options';
import { useQuery, type QueryKey } from '@tanstack/vue-query';
import { computed, toValue, type MaybeRefOrGetter } from 'vue';
import { useTeraApi } from '../../../client/useTeraApi';
import { landKeys } from '../queryKeys';
import { mapToModel, type Member } from './mapToModel';

/**
 * A page of the user's lands, `GET /api/lands`, with the API's own filters. The query
 * fetches again whenever they change.
 */
export function useGetLands(
  filters: MaybeRefOrGetter<ApiLandsGetCollectionData['query']> = {},
  options: ApiQueryOptions<Collection<Member>> = {},
) {
  const { api } = useTeraApi();

  return useQuery<Collection<Member>, Error, Collection<Member>, QueryKey>({
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
