// The only place that knows both the API's representation of a land and the front's
// model: an API change is fixed here, and the front never sees it. List the fields one by
// one, under the front's names: never spread the API's object into a model or a body.

import type {
  ApiLandsGetCollectionResponse,
  ApiLandsPostData,
  ApiLandsUlidGetResponse,
  ApiLandsUlidPatchData,
} from '@lychen/typescript-tera-api-sdk/generated/tera-api';
import {
  optional,
  required,
  toDate,
  withoutUndefined,
} from '@lychen/typescript-api-mapping/mapping';
import type {
  Land,
  LandCreateInput,
  LandSummary,
  LandUpdateInput,
} from '@lychen/typescript-tera-models/Land';

/** One land of a list (`GET /api/lands`). */
export function mapToSummaryModel(
  data: ApiLandsGetCollectionResponse['member'][number],
): LandSummary {
  return {
    ulid: required(data.ulid, 'land ulid'),
    name: data.name ?? '',
    surface: optional(data.surface),
    altitude: optional(data.altitude),
    createdAt: toDate(data.createdAt),
    updatedAt: toDate(data.updatedAt),
    // landMembers, landAreas and landSetting are IRIs of other resources: left out until a
    // screen needs them, then mapped to their ulids.
  };
}

/**
 * A single land: what `GET /api/lands/{ulid}` returns, and so do `POST`
 * and `PATCH`.
 */
export function mapToModel(data: ApiLandsUlidGetResponse): Land {
  // The same representation as in a list.
  return mapToSummaryModel(data);
}

/** The body of `POST /api/lands`. */
export function mapToCreateBody(input: LandCreateInput): ApiLandsPostData['body'] {
  return withoutUndefined({
    name: input.name,
    surface: input.surface,
    altitude: input.altitude,
  });
}

/** The JSON Merge Patch of `PATCH /api/lands/{ulid}`: only the fields set. */
export function mapToUpdateBody(input: LandUpdateInput): ApiLandsUlidPatchData['body'] {
  return withoutUndefined({
    name: input.name,
    surface: input.surface,
    altitude: input.altitude,
  });
}
