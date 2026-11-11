import type { ApiSeedLibrariesGetCollectionResponse } from '@lychen/typescript-flora-api-sdk/generated/flora-api';
import type { Collection } from '@lychen/typescript-api-mapping/Collection';
import { optional, required, toCollection, toDate } from '@lychen/typescript-api-mapping/mapping';
import type { SeedLibrarySummary } from '@lychen/typescript-flora-models/SeedLibrary';

/** The page this endpoint returns, as API Platform gives it, its members as the front's model. */
export function mapToModel(
  data: ApiSeedLibrariesGetCollectionResponse,
): Collection<SeedLibrarySummary> {
  return toCollection(data, mapToMember);
}

function mapToMember(
  data: ApiSeedLibrariesGetCollectionResponse['member'][number],
): SeedLibrarySummary {
  return {
    uuid: required(data.uuid, 'seed library uuid'),
    name: data.name ?? '',
    city: optional(data.city),
    seedLotUuids: data.seedLots ?? [],
    createdAt: toDate(data.createdAt),
    updatedAt: toDate(data.updatedAt),
  };
}
