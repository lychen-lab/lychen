import type { ApiSeedLibrariesGetCollectionResponse } from '@lychen/typescript-flora-api-sdk/generated/flora-api';
import type { Collection } from '@lychen/typescript-api-mapping/Collection';
import { optional, required, toCollection, toDate } from '@lychen/typescript-api-mapping/mapping';
import type { SeedLibrary } from '@lychen/typescript-front-model-business/seed/SeedLibrary';
import type { WithRequired } from '@lychen/typescript-front-model-business/WithRequired';

/** The fields of each member of the page this endpoint always gives. */
export type Member = WithRequired<SeedLibrary, 'id' | 'name' | 'seedLotIds'>;

/** The page this endpoint returns, as API Platform gives it, its members as the front's model. */
export function mapToModel(data: ApiSeedLibrariesGetCollectionResponse): Collection<Member> {
  return toCollection(data, mapToMember);
}

function mapToMember(data: ApiSeedLibrariesGetCollectionResponse['member'][number]): Member {
  return {
    id: required(data.uuid, 'seed library uuid'),
    name: data.name ?? '',
    city: optional(data.city),
    seedLotIds: data.seedLots ?? [],
    createdAt: toDate(data.createdAt),
    updatedAt: toDate(data.updatedAt),
  };
}
