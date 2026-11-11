import type { ApiAreaProposalsUuidGetResponse } from '@lychen/typescript-espace-api-sdk/generated/espace-api';
import type { AreaProposal } from '@lychen/typescript-espace-models/AreaProposal';
import { isDeletion } from '@lychen/vue-api-query/mercure/useMercure';
import type { ApiQueryOptions } from '@lychen/vue-api-query/options';
import { useQuery, useQueryClient, type QueryKey } from '@tanstack/vue-query';
import { computed, toValue, type MaybeRefOrGetter } from 'vue';
import { useEspaceApi } from '../../../client/useEspaceApi';
import { useEspaceMercure } from '../../../mercure/useEspaceMercure';
import { areaProposalKeys } from '../queryKeys';
import { mapToModel } from './mapToModel';

export type UseGetAreaProposalOptions = ApiQueryOptions<AreaProposal> & {
  /** Keeps the proposal in step with the changes anyone makes, pushed over Mercure. */
  live?: boolean;
};

/**
 * One area proposal, `GET /api/area_proposals/{uuid}`. The query waits for a uuid, and fetches again
 * whenever it changes.
 */
export function useGetAreaProposal(
  uuid: MaybeRefOrGetter<string | undefined>,
  options: UseGetAreaProposalOptions = {},
) {
  const { api } = useEspaceApi();
  const { enabled = true, live = false, ...queryOptions } = options;

  const query = useQuery<AreaProposal, Error, AreaProposal, QueryKey>({
    queryKey: computed(() => areaProposalKeys.detail(toValue(uuid) ?? '')),
    ...queryOptions,
    enabled: computed(() => Boolean(toValue(uuid)) && toValue(enabled)),
    async queryFn() {
      const current = toValue(uuid);
      if (!current) {
        throw new Error('useGetAreaProposal: there is no uuid to fetch yet.');
      }
      const { data } = await api.apiAreaProposalsUuidGet({
        path: { uuid: current },
        throwOnError: true,
      });
      return mapToModel(data);
    },
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
        // An update carries the proposal exactly as this endpoint returns it: no round trip.
        const proposal = mapToModel(update as ApiAreaProposalsUuidGetResponse);
        queryClient.setQueryData(areaProposalKeys.detail(proposal.uuid), proposal);
      },
    );
  }

  return query;
}
