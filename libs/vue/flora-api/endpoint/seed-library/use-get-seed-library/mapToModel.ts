import type { ApiSeedLibrariesUuidGetResponse } from '@lychen/typescript-flora-api-sdk/generated/flora-api';
import { optional, required, toDate } from '@lychen/typescript-api-mapping/mapping';
import type { SeedLibrary } from '@lychen/typescript-flora-models/SeedLibrary';

/** The seed library this endpoint returns, as the front's model. */
export function mapToModel(data: ApiSeedLibrariesUuidGetResponse): SeedLibrary {
  return {
    uuid: required(data.uuid, 'seed library uuid'),
    name: data.name ?? '',
    city: optional(data.city),
    seedLotUuids: data.seedLots ?? [],
    createdAt: toDate(data.createdAt),
    updatedAt: toDate(data.updatedAt),
  };
}
