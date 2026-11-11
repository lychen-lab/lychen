// The only place that knows both the API's representation of an area proposal and the
// front's model: an API change is fixed here, and the front never sees it. List the
// fields one by one, under the front's names: never spread the API's object into a
// model or a body.

import type {
  ApiAreaProposalsGetCollectionResponse,
  ApiAreaProposalsPostData,
  ApiAreaProposalsUuidGetResponse,
  ApiAreaProposalsUuidPatchData,
} from '@lychen/typescript-espace-api-sdk/generated/espace-api';
import {
  AREA_PROPOSAL_STATUSES,
  type AreaProposal,
  type AreaProposalCreateInput,
  type AreaProposalSummary,
  type AreaProposalUpdateInput,
} from '@lychen/typescript-espace-models/AreaProposal';
import {
  oneOf,
  optional,
  required,
  toDate,
  withoutUndefined,
} from '@lychen/typescript-api-mapping/mapping';
import { toPersonName } from '../person';

/** A proposal of a list (`GET /api/area_proposals`). */
export function mapToSummaryModel(
  data: ApiAreaProposalsGetCollectionResponse['member'][number],
): AreaProposalSummary {
  return {
    uuid: required(data.uuid, 'area proposal uuid'),
    title: data.title ?? '',
    description: data.description ?? '',
    surfaceToShare: optional(data.surfaceToShare),
    city: optional(data.city),
    altitude: optional(data.altitude),
    // The API exposes its workflow's marking: the front only knows a status.
    status: oneOf(AREA_PROPOSAL_STATUSES, data.place),
    activityCodes: data.activities ?? [],
    proposer: toPersonName(data.proposerFirstName, data.proposerLastName),
  };
}

/**
 * A single proposal: what `GET /api/area_proposals/{uuid}` returns, and so do `POST`,
 * `PATCH` and the Mercure updates.
 */
export function mapToModel(data: ApiAreaProposalsUuidGetResponse): AreaProposal {
  return {
    ...mapToSummaryModel(data),
    surfaceTotal: optional(data.surfaceTotal),
    createdAt: toDate(data.createdAt),
    archivedAt: toDate(data.archivedAt),
  };
}

/** The body of `POST /api/area_proposals`. */
export function mapToCreateBody(input: AreaProposalCreateInput): ApiAreaProposalsPostData['body'] {
  return withoutUndefined({
    uuid: input.uuid,
    title: input.title,
    description: input.description,
    surfaceTotal: input.surfaceTotal,
    surfaceToShare: input.surfaceToShare,
    city: input.city,
    altitude: input.altitude,
    activities: input.activityCodes ?? [],
  });
}

/** The JSON Merge Patch of `PATCH /api/area_proposals/{uuid}`: only the fields set. */
export function mapToUpdateBody(
  input: AreaProposalUpdateInput,
): ApiAreaProposalsUuidPatchData['body'] {
  return withoutUndefined({
    title: input.title,
    description: input.description,
    surfaceTotal: input.surfaceTotal,
    surfaceToShare: input.surfaceToShare,
    city: input.city,
    altitude: input.altitude,
    activities: input.activityCodes,
  });
}
