import type { ApiAreaRequestsGetCollectionResponse } from '@lychen/typescript-espace-api-sdk/generated/espace-api';
import type { Collection } from '@lychen/typescript-api-mapping/Collection';
import { oneOf, optional, required, toCollection } from '@lychen/typescript-api-mapping/mapping';
import {
  AREA_REQUEST_STATUS,
  type AreaRequest,
} from '@lychen/typescript-front-model-business/area/AreaRequest';
import type { WithRequired } from '@lychen/typescript-front-model-business/WithRequired';
import { toPersonName } from '../../person';

/** The fields of each member of the page this endpoint always gives. */
export type Member = WithRequired<AreaRequest, 'id' | 'title' | 'description' | 'activityCodes'>;

/** The page this endpoint returns, as API Platform gives it, its members as the front's model. */
export function mapToModel(data: ApiAreaRequestsGetCollectionResponse): Collection<Member> {
  return toCollection(data, mapToMember);
}

function mapToMember(data: ApiAreaRequestsGetCollectionResponse['member'][number]): Member {
  return {
    id: required(data.uuid, 'area request uuid'),
    title: data.title ?? '',
    description: data.description ?? '',
    minimalSurfaceRequested: optional(data.minimalSurfaceRequested),
    city: optional(data.city),
    // The API exposes its workflow's marking: the front only knows a status.
    status: oneOf(AREA_REQUEST_STATUS, data.place),
    activityCodes: data.activities ?? [],
    requester: toPersonName(data.requesterFirstName, data.requesterLastName),
  };
}
