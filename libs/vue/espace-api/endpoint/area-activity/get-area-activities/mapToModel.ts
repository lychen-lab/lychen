import type { ApiAreaActivitiesGetCollectionResponse } from '@lychen/typescript-espace-api-sdk/generated/espace-api';
import type { Collection } from '@lychen/typescript-api-mapping/Collection';
import { optional, required, toCollection } from '@lychen/typescript-api-mapping/mapping';
import type { AreaActivity } from '@lychen/typescript-espace-models/AreaActivity';

/** The page this endpoint returns, as API Platform gives it, its members as the front's model. */
export function mapToModel(data: ApiAreaActivitiesGetCollectionResponse): Collection<AreaActivity> {
  return toCollection(data, mapToMember);
}

function mapToMember(data: ApiAreaActivitiesGetCollectionResponse['member'][number]): AreaActivity {
  return {
    uuid: required(data.uuid, 'area activity uuid'),
    code: optional(data.code),
  };
}
