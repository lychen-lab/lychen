import type { ApiSeedLibrariesPostResponse } from '@lychen/typescript-flora-api-sdk/generated/flora-api';
import { optional, required, toDate } from '@lychen/typescript-api-mapping/mapping';
import type { SeedLibrary } from '@lychen/typescript-front-model-business/seed/SeedLibrary';
import type { WithRequired } from '@lychen/typescript-front-model-business/WithRequired';

/** The fields of the seed library this endpoint always gives. */
export type Model = WithRequired<SeedLibrary, 'id' | 'name' | 'seedLotIds'>;

/** The created seed library as the front's model. */
export function mapToModel(data: ApiSeedLibrariesPostResponse): Model {
  return {
    id: required(data.uuid, 'seed library uuid'),
    name: data.name ?? '',
    city: optional(data.city),
    seedLotIds: data.seedLots ?? [],
    createdAt: toDate(data.createdAt),
    updatedAt: toDate(data.updatedAt),
  };
}
