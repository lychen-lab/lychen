import type { ApiAreaProposalsUuidGetResponse } from '@lychen/typescript-espace-api-sdk/generated/espace-api';
import type { AreaProposal } from '@lychen/typescript-espace-models/AreaProposal';
import { isDeletion } from '@lychen/vue-api-query/mercure/useMercure';
import type { ApiQueryOptions } from '@lychen/vue-api-query/options';
import { useItemQuery } from '@lychen/vue-api-query/useItemQuery';
import { useQueryClient } from '@tanstack/vue-query';
import { toValue, type MaybeRefOrGetter } from 'vue';
import { useEspaceApi } from '../../client/useEspaceApi';
import { useEspaceMercure } from '../../mercure/useEspaceMercure';
import { mapToModel } from './mapper';
import { areaProposalKeys } from './queryKeys';

type Options = ApiQueryOptions<AreaProposal> & {
  /** Keeps the proposal in step with the changes anyone makes, pushed over Mercure. */
  live?: boolean;
};

/**
 * One area proposal, `GET /api/area_proposals/{uuid}`. The query waits for a uuid, and fetches again
 * whenever it changes.
 */
export function useGetAreaProposal(
  uuid: MaybeRefOrGetter<string | undefined>,
  options: Options = {},
) {
  const { api } = useEspaceApi();
  const { live = false, ...queryOptions } = options;

  const query = useItemQuery(
    uuid,
    {
      queryKey: areaProposalKeys.detail,
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
          void queryClient.invalidateQueries({
            queryKey: areaProposalKeys.detail(toValue(uuid) ?? ''),
          });
          return;
        }
        // An update carries the proposal exactly as GET returns it: no round trip.
        const proposal = mapToModel(update as ApiAreaProposalsUuidGetResponse);
        queryClient.setQueryData(areaProposalKeys.detail(proposal.uuid), proposal);
      },
    );
  }

  return query;
}
