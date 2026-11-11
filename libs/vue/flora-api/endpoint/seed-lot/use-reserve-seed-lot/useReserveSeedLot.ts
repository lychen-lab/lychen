import type { SeedLot } from '@lychen/typescript-flora-models/SeedLot';
import type { ApiMutationOptions } from '@lychen/vue-api-query/options';
import { useMutation, useQueryClient } from '@tanstack/vue-query';
import { useFloraApi } from '../../../client/useFloraApi';
import { seedLotKeys } from '../queryKeys';
import { mapToModel } from './mapToModel';

/**
 * Reserves a seed lot that is available, given its uuid: the `reserve` transition of its workflow
 * (`PATCH /api/seed_lots/{uuid}/reserve`, no body). It resolves with the lot, and by default
 * then refetches that lot and the lists: only the lot changes.
 */
export function useReserveSeedLot(options: ApiMutationOptions<SeedLot, string> = {}) {
  const { api } = useFloraApi();
  const queryClient = useQueryClient();

  return useMutation({
    onSuccess: (_, uuid) =>
      Promise.all([
        queryClient.invalidateQueries({ queryKey: seedLotKeys.detail(uuid) }),
        queryClient.invalidateQueries({ queryKey: seedLotKeys.lists() }),
      ]),
    ...options,
    async mutationFn(uuid: string) {
      const { data } = await api.apiSeedLotsUuidreservePatch({
        path: { uuid },
        throwOnError: true,
      });
      return mapToModel(data);
    },
  });
}
