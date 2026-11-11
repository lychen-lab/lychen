import type { ApiLandsGetCollectionResponse } from '@lychen/typescript-tera-api-sdk/generated/tera-api';
import type { Collection } from '@lychen/typescript-api-mapping/Collection';
import { optional, required, toCollection, toDate } from '@lychen/typescript-api-mapping/mapping';
import type { Land } from '@lychen/typescript-front-model-business/land/Land';
import type { WithRequired } from '@lychen/typescript-front-model-business/WithRequired';

/** The fields of each member of the page this endpoint always gives. */
export type Member = WithRequired<Land, 'id' | 'name'>;

/** The page this endpoint returns, as API Platform gives it, its members as the front's model. */
export function mapToModel(data: ApiLandsGetCollectionResponse): Collection<Member> {
  return toCollection(data, mapToMember);
}

function mapToMember(data: ApiLandsGetCollectionResponse['member'][number]): Member {
  return {
    id: required(data.ulid, 'land ulid'),
    name: data.name ?? '',
    surface: optional(data.surface),
    altitude: optional(data.altitude),
    createdAt: toDate(data.createdAt),
    updatedAt: toDate(data.updatedAt),
    // landMembers, landAreas and landSetting are IRIs of other resources: left out until a
    // screen needs them, then mapped to their ulids.
  };
}
