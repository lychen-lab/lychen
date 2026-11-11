// The only place that knows both the API's representation of a seed library and the front's
// model: an API change is fixed here, and the front never sees it. List the fields one by
// one, under the front's names: never spread the API's object into a model or a body.

import type {
  ApiSeedLibrariesGetCollectionResponse,
  ApiSeedLibrariesPostData,
  ApiSeedLibrariesUuidGetResponse,
  ApiSeedLibrariesUuidPatchData,
} from '@lychen/typescript-flora-api-sdk/generated/flora-api';
import {
  optional,
  required,
  toDate,
  withoutUndefined,
} from '@lychen/typescript-api-mapping/mapping';
import type {
  SeedLibrary,
  SeedLibraryCreateInput,
  SeedLibrarySummary,
  SeedLibraryUpdateInput,
} from '@lychen/typescript-flora-models/SeedLibrary';

/** One seed library of a list (`GET /api/seed_libraries`). */
export function mapToSummaryModel(
  data: ApiSeedLibrariesGetCollectionResponse['member'][number],
): SeedLibrarySummary {
  return {
    uuid: required(data.uuid, 'seed library uuid'),
    name: data.name ?? '',
    city: optional(data.city),
    seedLotUuids: data.seedLots ?? [],
    createdAt: toDate(data.createdAt),
    updatedAt: toDate(data.updatedAt),
  };
}

/**
 * A single seed library: what `GET /api/seed_libraries/{uuid}` returns, and so do `POST`
 * and `PATCH`.
 */
export function mapToModel(data: ApiSeedLibrariesUuidGetResponse): SeedLibrary {
  // The same representation as in a list.
  return mapToSummaryModel(data);
}

/** The body of `POST /api/seed_libraries`. */
export function mapToCreateBody(input: SeedLibraryCreateInput): ApiSeedLibrariesPostData['body'] {
  return withoutUndefined({
    name: input.name,
    city: input.city,
  });
}

/** The JSON Merge Patch of `PATCH /api/seed_libraries/{uuid}`: only the fields set. */
export function mapToUpdateBody(
  input: SeedLibraryUpdateInput,
): ApiSeedLibrariesUuidPatchData['body'] {
  return withoutUndefined({
    name: input.name,
    city: input.city,
  });
}
