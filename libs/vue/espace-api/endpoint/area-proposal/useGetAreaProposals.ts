import type { ApiAreaProposalsGetCollectionResponse } from '@lychen/typescript-espace-api-sdk/generated/espace-api';
import type { Collection } from '@lychen/typescript-api-mapping/Collection';
import {
  oneOf,
  optional,
  required,
  toCollection,
  withoutUndefined,
} from '@lychen/typescript-api-mapping/mapping';
import {
  AREA_PROPOSAL_STATUSES,
  type AreaProposalFilters,
  type AreaProposalSummary,
} from '@lychen/typescript-espace-models/AreaProposal';
import type { ApiQueryOptions } from '@lychen/vue-api-query/options';
import { useApiQuery } from '@lychen/vue-api-query/useApiQuery';
import { useQueryClient } from '@tanstack/vue-query';
import type { MaybeRefOrGetter } from 'vue';
import { useEspaceApi } from '../../client/useEspaceApi';
import { useEspaceMercure } from '../../mercure/useEspaceMercure';
import { toPersonName } from '../person';
import { areaProposalKeys } from './queryKeys';

type Options = ApiQueryOptions<Collection<AreaProposalSummary>> & {
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
  const { live = false, ...queryOptions } = options;

  const query = useApiQuery(
    filters,
    {
      queryKey: (params) => areaProposalKeys.of('get-area-proposals', params),
      async fetch(params) {
        const { data } = await api.apiAreaProposalsGetCollection({
          ...mapToRequest(params),
          throwOnError: true,
        });
        return toCollection(data, mapToModel);
      },
    },
    queryOptions,
  );

  if (live) {
    // A page is built from the collection representation, not the item one an update
    // carries, so refetch every page rather than patch them.
    const queryClient = useQueryClient();
    useEspaceMercure([{ match: '/api/area_proposals/:uuid', matchType: 'urlpattern' }], () =>
      queryClient.invalidateQueries({ queryKey: areaProposalKeys.endpoint('get-area-proposals') }),
    );
  }

  return query;
}

/** The filters under the API's names: the front's status is the API's workflow place. */
export function mapToRequest({ status, page, itemsPerPage }: AreaProposalFilters) {
  return { query: withoutUndefined({ place: status, page, itemsPerPage }) };
}

/** A proposal of the page as the front's model. */
export function mapToModel(
  data: ApiAreaProposalsGetCollectionResponse['member'][number],
): AreaProposalSummary {
  return {
    uuid: required(data.uuid, 'area proposal uuid'),
    title: data.title ?? '',
    description: data.description ?? '',
    surfaceToShare: optional(data.surfaceToShare),
    city: optional(data.city),
    altitude: optional(data.altitude),
    // The API exposes its workflow's marking: the front only knows a status.
    status: oneOf(AREA_PROPOSAL_STATUSES, data.place),
    activityCodes: data.activities ?? [],
    proposer: toPersonName(data.proposerFirstName, data.proposerLastName),
  };
}
