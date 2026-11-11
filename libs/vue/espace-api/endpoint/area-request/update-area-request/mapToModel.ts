import type { ApiAreaRequestsUuidPatchResponse } from '@lychen/typescript-espace-api-sdk/generated/espace-api';
import { oneOf, optional, required, toDate } from '@lychen/typescript-api-mapping/mapping';
import {
  AREA_REQUEST_STATUS,
  type AreaRequest,
} from '@lychen/typescript-front-model-business/area/AreaRequest';
import type { WithRequired } from '@lychen/typescript-front-model-business/WithRequired';
import { toPersonName } from '../../person';

/** The fields of the area request this endpoint always gives. */
export type Model = WithRequired<AreaRequest, 'id' | 'title' | 'description' | 'activityCodes'>;

/** The changed area request as the front's model. */
export function mapToModel(data: ApiAreaRequestsUuidPatchResponse): Model {
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
    createdAt: toDate(data.createdAt),
    archivedAt: toDate(data.archivedAt),
  };
}
