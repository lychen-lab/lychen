import type { ApiSeedLotsUuidgivePatchResponse } from '@lychen/typescript-flora-api-sdk/generated/flora-api';
import { oneOf, optional, required, toDate } from '@lychen/typescript-api-mapping/mapping';
import { SEED_LOT_STATE, type SeedLot } from '@lychen/typescript-front-model-business/seed/SeedLot';
import type { WithRequired } from '@lychen/typescript-front-model-business/WithRequired';

/** The fields of the seed lot this endpoint always gives. */
export type Model = WithRequired<SeedLot, 'id' | 'variety' | 'characteristicIds'>;

/** The lot, once the `give` transition applied, as the front's model. */
export function mapToModel(data: ApiSeedLotsUuidgivePatchResponse): Model {
  return {
    id: required(data.uuid, 'seed lot uuid'),
    variety: data.variety ?? '',
    harvestYear: optional(data.harvestYear),
    quantityInGrams: optional(data.quantityInGrams),
    seedLibraryId: optional(data.seedLibrary),
    characteristicIds: data.characteristics ?? [],
    // The API exposes its workflow's marking: the front only knows a state.
    state: oneOf(SEED_LOT_STATE, data.state),
    createdAt: toDate(data.createdAt),
    updatedAt: toDate(data.updatedAt),
  };
}
