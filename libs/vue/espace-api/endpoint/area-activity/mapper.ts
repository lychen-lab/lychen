// The only place that knows both the API's representation of an area activity and the
// front's model: an API change is fixed here, and the front never sees it. List the
// fields one by one, under the front's names: never spread the API's object into a
// model or a body.

import type {
  ApiAreaActivitiesGetCollectionResponse,
  ApiAreaActivitiesPostData,
  ApiAreaActivitiesUuidGetResponse,
  ApiAreaActivitiesUuidPatchData,
} from '@lychen/typescript-espace-api-sdk/generated/espace-api';
import type {
  AreaActivity,
  AreaActivityCreateInput,
  AreaActivityUpdateInput,
} from '@lychen/typescript-espace-models/AreaActivity';
import {
  optional,
  required,
  toDate,
  withoutUndefined,
} from '@lychen/typescript-api-mapping/mapping';

/** An activity of a list (`GET /api/area_activities`). */
export function mapToSummaryModel(
  data: ApiAreaActivitiesGetCollectionResponse['member'][number],
): AreaActivity {
  return {
    uuid: required(data.uuid, 'area activity uuid'),
    code: optional(data.code),
  };
}

/**
 * A single activity: what `GET /api/area_activities/{uuid}` returns, and so do `POST`
 * and `PATCH`. The API leaves its code out of it.
 */
export function mapToModel(data: ApiAreaActivitiesUuidGetResponse): AreaActivity {
  return {
    uuid: required(data.uuid, 'area activity uuid'),
    createdAt: toDate(data.createdAt),
  };
}

/** The body of `POST /api/area_activities`. */
export function mapToCreateBody(input: AreaActivityCreateInput): ApiAreaActivitiesPostData['body'] {
  return withoutUndefined({ uuid: input.uuid, code: input.code });
}

/** The JSON Merge Patch of `PATCH /api/area_activities/{uuid}`: only the fields set. */
export function mapToUpdateBody(
  input: AreaActivityUpdateInput,
): ApiAreaActivitiesUuidPatchData['body'] {
  return withoutUndefined({ code: input.code });
}
