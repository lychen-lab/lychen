import type { ApiAreaRequestsUuidPatchResponse } from '@lychen/typescript-espace-api-sdk/generated/espace-api';
import { oneOf, optional, required, toDate } from '@lychen/typescript-api-mapping/mapping';
import {
  AREA_REQUEST_STATUSES,
  type AreaRequest,
} from '@lychen/typescript-espace-models/AreaRequest';
import { toPersonName } from '../../person';

/** The changed area request as the front's model. */
export function mapToModel(data: ApiAreaRequestsUuidPatchResponse): AreaRequest {
  return {
    uuid: required(data.uuid, 'area request uuid'),
    title: data.title ?? '',
    description: data.description ?? '',
    minimalSurfaceRequested: optional(data.minimalSurfaceRequested),
    city: optional(data.city),
    // The API exposes its workflow's marking: the front only knows a status.
    status: oneOf(AREA_REQUEST_STATUSES, data.place),
    activityCodes: data.activities ?? [],
    requester: toPersonName(data.requesterFirstName, data.requesterLastName),
    createdAt: toDate(data.createdAt),
    archivedAt: toDate(data.archivedAt),
  };
}
