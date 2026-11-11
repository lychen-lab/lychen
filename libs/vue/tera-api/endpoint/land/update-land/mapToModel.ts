import type { ApiLandsUlidPatchResponse } from '@lychen/typescript-tera-api-sdk/generated/tera-api';
import { optional, required, toDate } from '@lychen/typescript-api-mapping/mapping';
import type { Land } from '@lychen/typescript-front-model-business/land/Land';
import type { WithRequired } from '@lychen/typescript-front-model-business/WithRequired';

/** The fields of the land this endpoint always gives. */
export type Model = WithRequired<Land, 'id' | 'name'>;

/** The changed land as the front's model. */
export function mapToModel(data: ApiLandsUlidPatchResponse): Model {
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
