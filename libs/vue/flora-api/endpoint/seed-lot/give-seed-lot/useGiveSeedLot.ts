import type { ApiMutationOptions } from '@lychen/vue-api-query/options';
import { useMutation, useQueryClient } from '@tanstack/vue-query';
import { useFloraApi } from '../../../client/useFloraApi';
import { seedLotKeys } from '../queryKeys';
import { mapToModel, type Model } from './mapToModel';

/**
 * Gives a seed lot away, given its id: the `give` transition of its workflow
 * (`PATCH /api/seed_lots/{uuid}/give`, no body). It resolves with the lot, and by default
 * then refetches that lot and the lists: only the lot changes.
 */
export function useGiveSeedLot(options: ApiMutationOptions<Model, string> = {}) {
  const { api } = useFloraApi();
  const queryClient = useQueryClient();

  return useMutation({
    onSuccess: (_, id) =>
      Promise.all([
        queryClient.invalidateQueries({ queryKey: seedLotKeys.detail(id) }),
        queryClient.invalidateQueries({ queryKey: seedLotKeys.lists() }),
      ]),
    ...options,
    async mutationFn(id: string) {
      const { data } = await api.apiSeedLotsUuidgivePatch({
        path: { uuid: id },
        throwOnError: true,
      });
      return mapToModel(data);
    },
  });
}
