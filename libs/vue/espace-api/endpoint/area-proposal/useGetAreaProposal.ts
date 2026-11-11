import type { ApiAreaProposalsUuidGetResponse } from '@lychen/typescript-espace-api-sdk/generated/espace-api';
import { oneOf, optional, required, toDate } from '@lychen/typescript-api-mapping/mapping';
import {
  AREA_PROPOSAL_STATUSES,
  type AreaProposal,
} from '@lychen/typescript-espace-models/AreaProposal';
import { isDeletion } from '@lychen/vue-api-query/mercure/useMercure';
import type { ApiQueryOptions } from '@lychen/vue-api-query/options';
import { useApiQuery } from '@lychen/vue-api-query/useApiQuery';
import { useQueryClient } from '@tanstack/vue-query';
import { toValue, type MaybeRefOrGetter } from 'vue';
import { useEspaceApi } from '../../client/useEspaceApi';
import { useEspaceMercure } from '../../mercure/useEspaceMercure';
import { toPersonName } from '../person';
import { areaProposalKeys } from './queryKeys';

type Options = ApiQueryOptions<AreaProposal> & {
  /** Keeps the proposal in step with the changes anyone makes, pushed over Mercure. */
  live?: boolean;
};

/** The cache key of `useGetAreaProposal`. */
export function getAreaProposalQueryKey(uuid: string | undefined) {
  return areaProposalKeys.of('get-area-proposal', uuid);
}

/**
 * One area proposal, `GET /api/area_proposals/{uuid}`. The query waits for a uuid, and
 * fetches again whenever it changes.
 */
export function useGetAreaProposal(
  uuid: MaybeRefOrGetter<string | undefined>,
  options: Options = {},
) {
  const { api } = useEspaceApi();
  const { live = false, ...queryOptions } = options;

  const query = useApiQuery(
    uuid,
    {
      queryKey: getAreaProposalQueryKey,
      async fetch(id) {
        const { data } = await api.apiAreaProposalsUuidGet({
          path: { uuid: id },
          throwOnError: true,
        });
        return mapToModel(data);
      },
    },
    queryOptions,
  );

  if (live) {
    const queryClient = useQueryClient();
    useEspaceMercure(
      () => {
        const current = toValue(uuid);
        return current ? [{ match: `/api/area_proposals/${current}` }] : [];
      },
      (update) => {
        if (isDeletion(update)) {
          // Nothing left to show: the refetch tells the query (a 404).
          void queryClient.invalidateQueries({ queryKey: getAreaProposalQueryKey(toValue(uuid)) });
          return;
        }
        // An update carries the proposal exactly as this endpoint returns it: no round trip.
        const proposal = mapToModel(update as ApiAreaProposalsUuidGetResponse);
        queryClient.setQueryData(getAreaProposalQueryKey(proposal.uuid), proposal);
      },
    );
  }

  return query;
}

/** What this endpoint returns, and Mercure pushes, as the front's model. */
export function mapToModel(data: ApiAreaProposalsUuidGetResponse): AreaProposal {
  return {
    uuid: required(data.uuid, 'area proposal uuid'),
    title: data.title ?? '',
    description: data.description ?? '',
    surfaceTotal: optional(data.surfaceTotal),
    surfaceToShare: optional(data.surfaceToShare),
    city: optional(data.city),
    altitude: optional(data.altitude),
    // The API exposes its workflow's marking: the front only knows a status.
    status: oneOf(AREA_PROPOSAL_STATUSES, data.place),
    activityCodes: data.activities ?? [],
    proposer: toPersonName(data.proposerFirstName, data.proposerLastName),
    createdAt: toDate(data.createdAt),
    archivedAt: toDate(data.archivedAt),
  };
}
