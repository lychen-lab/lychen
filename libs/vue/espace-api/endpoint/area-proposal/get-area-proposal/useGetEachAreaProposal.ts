import type { ApiAreaProposalsUuidGetResponse } from '@lychen/typescript-espace-api-sdk/generated/espace-api';
import { isDeletion } from '@lychen/vue-api-query/mercure/useMercure';
import type { ApiQueryOptions } from '@lychen/vue-api-query/options';
import {
  useQueries,
  useQueryClient,
  type QueryKey,
  type UseQueryOptions,
} from '@tanstack/vue-query';
import { computed, toValue, type MaybeRefOrGetter } from 'vue';
import { useEspaceApi } from '../../../client/useEspaceApi';
import { useEspaceMercure } from '../../../mercure/useEspaceMercure';
import { areaProposalKeys } from '../queryKeys';
import { mapToModel, type Model } from './mapToModel';

export type UseGetEachAreaProposalOptions = Omit<ApiQueryOptions<Model>, 'queryKey'> & {
  /** Keeps the proposals in step with the changes anyone makes, pushed over Mercure. */
  live?: boolean;
};

/**
 * Several area proposals, one `GET /api/area_proposals/{uuid}` each, for a list of ids the
 * collection cannot filter on. One query per id, in the order of the ids, under the key of
 * `useGetAreaProposal`: both share their cache.
 */
export function useGetEachAreaProposal(
  ids: MaybeRefOrGetter<readonly string[]>,
  options: UseGetEachAreaProposalOptions = {},
) {
  const { api } = useEspaceApi();
  const { live = false, ...queryOptions } = options;

  const queries = useQueries<UseQueryOptions<Model, Error, Model, Model, QueryKey>[]>({
    queries: computed(() =>
      toValue(ids).map((id): UseQueryOptions<Model, Error, Model, Model, QueryKey> => ({
        ...queryOptions,
        queryKey: areaProposalKeys.detail(id),
        async queryFn(): Promise<Model> {
          const { data } = await api.apiAreaProposalsUuidGet({
            path: { uuid: id },
            throwOnError: true,
          });
          return mapToModel(data);
        },
      })),
    ),
  });

  if (live) {
    // One subscription for every proposal, rather than one per id.
    const queryClient = useQueryClient();
    useEspaceMercure(
      [{ match: '/api/area_proposals/:uuid', matchType: 'urlpattern' }],
      (update) => {
        const id = update['@id'].split('/').pop() ?? '';
        if (!toValue(ids).includes(id)) {
          return;
        }
        if (isDeletion(update)) {
          // Nothing left to show: the refetch tells the query (a 404).
          void queryClient.invalidateQueries({ queryKey: areaProposalKeys.detail(id) });
          return;
        }
        // An update carries the proposal exactly as this endpoint returns it: no round trip.
        const proposal = mapToModel(update as ApiAreaProposalsUuidGetResponse);
        queryClient.setQueryData(areaProposalKeys.detail(proposal.id), proposal);
      },
    );
  }

  return queries;
}
