// The only place that knows both the API's representation of an area activity and the
// front's model: an API change is fixed here, and the front never sees it.

import type {
  AreaActivityAreaActivityCollectionJsonld,
  AreaActivityAreaActivityPatchJsonMergePatch,
  AreaActivityAreaActivityPost,
  AreaActivityJsonld,
} from '@lychen/typescript-espace-api-sdk/generated/espace-api';
import type {
  AreaActivity,
  AreaActivityCreateInput,
  AreaActivityUpdateInput,
} from '@lychen/typescript-espace/models/AreaActivity';
import { optional, required, toDate, withoutUndefined } from '../mapping';

/** An activity of a list (`GET /api/area_activities`). */
export function mapToSummaryModel(data: AreaActivityAreaActivityCollectionJsonld): AreaActivity {
  return {
    uuid: required(data.uuid, 'area activity uuid'),
    code: optional(data.code),
  };
}

/**
 * A single activity: what `GET /api/area_activities/{uuid}` returns, and so do `POST`
 * and `PATCH`. The API leaves its code out of it.
 */
export function mapToModel(data: AreaActivityJsonld): AreaActivity {
  return {
    uuid: required(data.uuid, 'area activity uuid'),
    createdAt: toDate(data.createdAt),
  };
}

/** The body of `POST /api/area_activities`. */
export function mapToCreateBody(input: AreaActivityCreateInput): AreaActivityAreaActivityPost {
  return withoutUndefined({ uuid: input.uuid, code: input.code });
}

/** The JSON Merge Patch of `PATCH /api/area_activities/{uuid}`: only the fields set. */
export function mapToUpdateBody(
  input: AreaActivityUpdateInput,
): AreaActivityAreaActivityPatchJsonMergePatch {
  return withoutUndefined({ code: input.code });
}
