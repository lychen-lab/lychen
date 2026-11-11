// The only place that knows both the API's representation of an area request and the
// front's model: an API change is fixed here, and the front never sees it. List the
// fields one by one, under the front's names: never spread the API's object into a
// model or a body.

import type {
  ApiAreaRequestsGetCollectionResponse,
  ApiAreaRequestsPostData,
  ApiAreaRequestsUuidGetResponse,
  ApiAreaRequestsUuidPatchData,
} from '@lychen/typescript-espace-api-sdk/generated/espace-api';
import {
  AREA_REQUEST_STATUSES,
  type AreaRequest,
  type AreaRequestCreateInput,
  type AreaRequestSummary,
  type AreaRequestUpdateInput,
} from '@lychen/typescript-espace-models/AreaRequest';
import {
  oneOf,
  optional,
  required,
  toDate,
  withoutUndefined,
} from '@lychen/typescript-api-mapping/mapping';
import { toPersonName } from '../person';

/** A request of a list (`GET /api/area_requests`). */
export function mapToSummaryModel(
  data: ApiAreaRequestsGetCollectionResponse['member'][number],
): AreaRequestSummary {
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
  };
}

/**
 * A single request: what `GET /api/area_requests/{uuid}` returns, and so do `POST` and
 * `PATCH`.
 */
export function mapToModel(data: ApiAreaRequestsUuidGetResponse): AreaRequest {
  return {
    ...mapToSummaryModel(data),
    createdAt: toDate(data.createdAt),
    archivedAt: toDate(data.archivedAt),
  };
}

/** The body of `POST /api/area_requests`. */
export function mapToCreateBody(input: AreaRequestCreateInput): ApiAreaRequestsPostData['body'] {
  return withoutUndefined({
    uuid: input.uuid,
    title: input.title,
    description: input.description,
    minimalSurfaceRequested: input.minimalSurfaceRequested,
    city: input.city,
    activities: input.activityCodes ?? [],
  });
}

/** The JSON Merge Patch of `PATCH /api/area_requests/{uuid}`: only the fields set. */
export function mapToUpdateBody(
  input: AreaRequestUpdateInput,
): ApiAreaRequestsUuidPatchData['body'] {
  return withoutUndefined({
    title: input.title,
    description: input.description,
    minimalSurfaceRequested: input.minimalSurfaceRequested,
    city: input.city,
    activities: input.activityCodes,
  });
}
