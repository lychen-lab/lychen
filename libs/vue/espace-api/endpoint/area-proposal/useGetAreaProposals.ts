import { useQuery, useQueryClient, type QueryKey } from '@tanstack/vue-query';
import { computed, toValue, type MaybeRefOrGetter } from 'vue';
import type {
  AreaProposalFilters,
  AreaProposalSummary,
} from '@lychen/typescript-espace/models/AreaProposal';
import type { Collection } from '@lychen/typescript-espace/models/Collection';
import { useEspaceApi } from '../../client/useEspaceApi';
import { useEspaceMercure } from '../../mercure/useEspaceMercure';
import { toCollection, withoutUndefined } from '../mapping';
import type { EspaceQueryOptions } from '../options';
import { mapToSummaryModel } from './mapper';
import { areaProposalKeys } from './queryKeys';

type Options = EspaceQueryOptions<Collection<AreaProposalSummary>> & {
  /** Refetches the list whenever anyone creates, changes or deletes a proposal. */
  live?: boolean;
};

/**
 * A page of the area proposals the user may see, `GET /api/area_proposals`. The query
 * fetches again whenever the filters change.
 */
export function useGetAreaProposals(
  filters: MaybeRefOrGetter<AreaProposalFilters> = {},
  options: Options = {},
) {
  const { api } = useEspaceApi();
  const { enabled = true, live = false, ...queryOptions } = options;

  const query = useQuery<
    Collection<AreaProposalSummary>,
    Error,
    Collection<AreaProposalSummary>,
    QueryKey
  >({
    ...queryOptions,
    queryKey: computed(() => areaProposalKeys.list(toValue(filters))),
    async queryFn() {
      const { status, page, itemsPerPage } = toValue(filters);
      const { data } = await api.apiAreaProposalsGetCollection({
        query: withoutUndefined({ place: status, page, itemsPerPage }),
        throwOnError: true,
      });
      return toCollection(data, mapToSummaryModel);
    },
    enabled,
  });

  if (live) {
    // A list is built from the collection representation, not the item one an update
    // carries, so refetch it rather than patch it.
    const queryClient = useQueryClient();
    useEspaceMercure([{ match: '/api/area_proposals/:uuid', matchType: 'urlpattern' }], () =>
      queryClient.invalidateQueries({ queryKey: areaProposalKeys.lists() }),
    );
  }

  return query;
}
