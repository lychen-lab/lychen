import type { ApiAreaActivitiesGetCollectionResponse } from '@lychen/typescript-espace-api-sdk/generated/espace-api';
import type { Collection } from '@lychen/typescript-api-mapping/Collection';
import { optional, required, toCollection } from '@lychen/typescript-api-mapping/mapping';
import type { AreaActivity } from '@lychen/typescript-front-model-business/area/AreaActivity';
import type { WithRequired } from '@lychen/typescript-front-model-business/WithRequired';

/** The fields of each member of the page this endpoint always gives. */
export type Member = WithRequired<AreaActivity, 'id'>;

/** The page this endpoint returns, as API Platform gives it, its members as the front's model. */
export function mapToModel(data: ApiAreaActivitiesGetCollectionResponse): Collection<Member> {
  return toCollection(data, mapToMember);
}

function mapToMember(data: ApiAreaActivitiesGetCollectionResponse['member'][number]): Member {
  return {
    id: required(data.uuid, 'area activity uuid'),
    code: optional(data.code),
  };
}
