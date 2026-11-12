import type { ApiAreaProposalsGetCollectionData } from '@lychen/typescript-espace-api-sdk/generated/espace-api';
import type { Collection } from '@lychen/typescript-api-mapping/Collection';
import type { ApiQueryOptions } from '@lychen/vue-api-query/options';
import { useQuery, useQueryClient, type QueryKey } from '@tanstack/vue-query';
import { computed, toValue, type MaybeRefOrGetter } from 'vue';
import { useEspaceApi } from '../../../client/useEspaceApi';
import { useEspaceMercure } from '../../../mercure/useEspaceMercure';
import { areaProposalKeys } from '../queryKeys';
import { mapToModel, type Member } from './mapToModel';

export type UseGetAreaProposalsOptions = ApiQueryOptions<Collection<Member>> & {
  /** Refetches the pages whenever anyone creates, changes or deletes a proposal. */
  live?: boolean;
};

/**
 * A page of the area proposals the user may see, `GET /api/area_proposals`, with the API's own filters. The
 * query fetches again whenever they change.
 */
export function useGetAreaProposals(
  filters: MaybeRefOrGetter<ApiAreaProposalsGetCollectionData['query']> = {},
  options: UseGetAreaProposalsOptions = {},
) {
  const { api } = useEspaceApi();
  const { live = false, ...queryOptions } = options;

  const query = useQuery<Collection<Member>, Error, Collection<Member>, QueryKey>({
    queryKey: computed(() => areaProposalKeys.list(toValue(filters))),
    ...queryOptions,
    async queryFn() {
      const { data } = await api.apiAreaProposalsGetCollection({
        query: toValue(filters),
        throwOnError: true,
      });
      return mapToModel(data);
    },
  });

  if (live) {
    // A page is built from the collection representation, not the item one an update
    // carries, so refetch every page rather than patch them.
    const queryClient = useQueryClient();
    useEspaceMercure([{ match: '/api/area_proposals/:uuid', matchType: 'urlpattern' }], () =>
      queryClient.invalidateQueries({ queryKey: areaProposalKeys.lists() }),
    );
  }

  return query;
}
