import { toCollection, withoutUndefined } from '@lychen/typescript-api-mapping/mapping';
import type { Collection } from '@lychen/typescript-api-mapping/Collection';
import type {
  AreaProposalFilters,
  AreaProposalSummary,
} from '@lychen/typescript-espace-models/AreaProposal';
import type { ApiQueryOptions } from '@lychen/vue-api-query/options';
import { useCollectionQuery } from '@lychen/vue-api-query/useCollectionQuery';
import { useQueryClient } from '@tanstack/vue-query';
import type { MaybeRefOrGetter } from 'vue';
import { useEspaceApi } from '../../client/useEspaceApi';
import { useEspaceMercure } from '../../mercure/useEspaceMercure';
import { mapToSummaryModel } from './mapper';
import { areaProposalKeys } from './queryKeys';

type Options = ApiQueryOptions<Collection<AreaProposalSummary>> & {
  /** Refetches the list whenever anyone creates, changes or deletes a proposal. */
  live?: boolean;
};

/**
 * A page of the area proposals the user may see, `GET /api/area_proposals`. The query fetches again whenever the
 * filters change.
 */
export function useGetAreaProposals(
  filters: MaybeRefOrGetter<AreaProposalFilters> = {},
  options: Options = {},
) {
  const { api } = useEspaceApi();
  const { live = false, ...queryOptions } = options;

  const query = useCollectionQuery(
    filters,
    {
      queryKey: areaProposalKeys.list,
      async fetch({ status, page, itemsPerPage }) {
        const { data } = await api.apiAreaProposalsGetCollection({
          query: withoutUndefined({ place: status, page, itemsPerPage }),
          throwOnError: true,
        });
        return toCollection(data, mapToSummaryModel);
      },
    },
    queryOptions,
  );

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
