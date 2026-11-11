import type {
  ApiSeedLotsUuidgivePatchData,
  ApiSeedLotsUuidgivePatchResponse,
} from '@lychen/typescript-flora-api-sdk/generated/flora-api';
import { oneOf, optional, required, toDate } from '@lychen/typescript-api-mapping/mapping';
import { SEED_LOT_STATUSES, type SeedLot } from '@lychen/typescript-flora-models/SeedLot';
import type { ApiMutationOptions } from '@lychen/vue-api-query/options';
import { useApiMutation } from '@lychen/vue-api-query/useApiMutation';
import { useFloraApi } from '../../client/useFloraApi';
import { seedLotKeys } from './queryKeys';

/** The lot's uuid. */
export type GiveSeedLotVariables = string;

/** Gives a seed lot away: the `give` transition of its workflow
 * (`PATCH /api/seed_lots/{uuid}/give`), which resolves with the lot. */
export function useGiveSeedLot(options: ApiMutationOptions<SeedLot, GiveSeedLotVariables> = {}) {
  const { api } = useFloraApi();

  return useApiMutation(
    {
      async send(variables: GiveSeedLotVariables) {
        const { data } = await api.apiSeedLotsUuidgivePatch({
          ...mapToRequest(variables),
          throwOnError: true,
        });
        return mapToModel(data);
      },
      // Only the lot changes: a library shows its lots' statuses from the seed lot queries.
      invalidates: () => [seedLotKeys.all],
    },
    options,
  );
}

/** The request, from the front's input, under the API's names. */
export function mapToRequest(
  variables: GiveSeedLotVariables,
): Omit<ApiSeedLotsUuidgivePatchData, 'url'> {
  return {
    // A transition takes no body.
    path: { uuid: variables },
  };
}

/**
 * The response as the front's model: list its fields one by one, under the front's names,
 * never with a spread.
 */
export function mapToModel(data: ApiSeedLotsUuidgivePatchResponse): SeedLot {
  return {
    uuid: required(data.uuid, 'seed lot uuid'),
    variety: data.variety ?? '',
    harvestYear: optional(data.harvestYear),
    quantityInGrams: optional(data.quantityInGrams),
    seedLibraryUuid: optional(data.seedLibrary),
    characteristicUuids: data.characteristics ?? [],
    // The API exposes its workflow's marking: the front only knows a status.
    status: oneOf(SEED_LOT_STATUSES, data.state),
    createdAt: toDate(data.createdAt),
    updatedAt: toDate(data.updatedAt),
  };
}
