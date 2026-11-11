import type { ApiAreaProposalsUuidGetResponse } from '@lychen/typescript-espace-api-sdk/generated/espace-api';
import { isDeletion } from '@lychen/vue-api-query/mercure/useMercure';
import type { ApiQueryOptions } from '@lychen/vue-api-query/options';
import { useQuery, useQueryClient, type QueryKey } from '@tanstack/vue-query';
import { computed, toValue, type MaybeRefOrGetter } from 'vue';
import { useEspaceApi } from '../../../client/useEspaceApi';
import { useEspaceMercure } from '../../../mercure/useEspaceMercure';
import { areaProposalKeys } from '../queryKeys';
import { mapToModel, type Model } from './mapToModel';

export type UseGetAreaProposalOptions = ApiQueryOptions<Model> & {
  /** Keeps the proposal in step with the changes anyone makes, pushed over Mercure. */
  live?: boolean;
};

/**
 * One area proposal, `GET /api/area_proposals/{uuid}`. The query waits for an id, and fetches again
 * whenever it changes.
 */
export function useGetAreaProposal(
  id: MaybeRefOrGetter<string | undefined>,
  options: UseGetAreaProposalOptions = {},
) {
  const { api } = useEspaceApi();
  const { enabled = true, live = false, ...queryOptions } = options;

  const query = useQuery<Model, Error, Model, QueryKey>({
    queryKey: computed(() => areaProposalKeys.detail(toValue(id) ?? '')),
    ...queryOptions,
    enabled: computed(() => Boolean(toValue(id)) && toValue(enabled)),
    async queryFn() {
      const current = toValue(id);
      if (!current) {
        throw new Error('useGetAreaProposal: there is no id to fetch yet.');
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
        const current = toValue(id);
        return current ? [{ match: `/api/area_proposals/${current}` }] : [];
      },
      (update) => {
        if (isDeletion(update)) {
          // Nothing left to show: the refetch tells the query (a 404).
          void queryClient.invalidateQueries({
            queryKey: areaProposalKeys.detail(toValue(id) ?? ''),
          });
          return;
        }
        // An update carries the proposal exactly as this endpoint returns it: no round trip.
        const proposal = mapToModel(update as ApiAreaProposalsUuidGetResponse);
        queryClient.setQueryData(areaProposalKeys.detail(proposal.id), proposal);
      },
    );
  }

  return query;
}
