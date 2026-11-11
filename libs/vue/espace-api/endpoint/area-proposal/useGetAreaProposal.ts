import { useQuery, useQueryClient, type QueryKey } from '@tanstack/vue-query';
import { computed, toValue, type MaybeRefOrGetter } from 'vue';
import type { AreaProposalJsonld } from '@lychen/typescript-espace-api-sdk/generated/espace-api';
import type { AreaProposal } from '@lychen/typescript-espace/models/AreaProposal';
import { useEspaceApi } from '../../client/useEspaceApi';
import { isDeletion, useEspaceMercure } from '../../mercure/useEspaceMercure';
import { required } from '../mapping';
import type { EspaceQueryOptions } from '../options';
import { mapToModel } from './mapper';
import { areaProposalKeys } from './queryKeys';

type Options = EspaceQueryOptions<AreaProposal> & {
  /** Keeps the proposal in step with the changes anyone makes, pushed over Mercure. */
  live?: boolean;
};

/**
 * One area proposal, `GET /api/area_proposals/{uuid}`. The query waits for a uuid, and
 * fetches again whenever it changes.
 */
export function useGetAreaProposal(
  uuid: MaybeRefOrGetter<string | undefined>,
  options: Options = {},
) {
  const { api } = useEspaceApi();
  const { enabled = true, live = false, ...queryOptions } = options;

  const query = useQuery<AreaProposal, Error, AreaProposal, QueryKey>({
    ...queryOptions,
    queryKey: computed(() => areaProposalKeys.detail(toValue(uuid) ?? '')),
    async queryFn() {
      const { data } = await api.apiAreaProposalsUuidGet({
        path: { uuid: required(toValue(uuid), 'uuid to fetch the area proposal of') },
        throwOnError: true,
      });
      return mapToModel(data);
    },
    enabled: computed(() => Boolean(toValue(uuid)) && toValue(enabled)),
  });

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
          void queryClient.invalidateQueries({
            queryKey: areaProposalKeys.detail(toValue(uuid) ?? ''),
          });
          return;
        }
        // An update carries the proposal exactly as GET returns it: no round trip.
        const proposal = mapToModel(update as AreaProposalJsonld);
        queryClient.setQueryData(areaProposalKeys.detail(proposal.uuid), proposal);
      },
    );
  }

  return query;
}
