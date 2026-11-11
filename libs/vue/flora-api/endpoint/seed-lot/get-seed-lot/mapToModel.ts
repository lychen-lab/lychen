import type { ApiSeedLotsUuidGetResponse } from '@lychen/typescript-flora-api-sdk/generated/flora-api';
import { oneOf, optional, required, toDate } from '@lychen/typescript-api-mapping/mapping';
import { SEED_LOT_STATUSES, type SeedLot } from '@lychen/typescript-flora-models/SeedLot';

/** The seed lot this endpoint returns, as the front's model. */
export function mapToModel(data: ApiSeedLotsUuidGetResponse): SeedLot {
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
