import type { ApiAreaProposalsUuidGetResponse } from '@lychen/typescript-espace-api-sdk/generated/espace-api';
import { oneOf, optional, required, toDate } from '@lychen/typescript-api-mapping/mapping';
import {
  AREA_PROPOSAL_STATUSES,
  type AreaProposal,
} from '@lychen/typescript-espace-models/AreaProposal';
import { toPersonName } from '../../person';

/** The area proposal this endpoint returns, and Mercure pushes, as the front's model. */
export function mapToModel(data: ApiAreaProposalsUuidGetResponse): AreaProposal {
  return {
    uuid: required(data.uuid, 'area proposal uuid'),
    title: data.title ?? '',
    description: data.description ?? '',
    surfaceTotal: optional(data.surfaceTotal),
    surfaceToShare: optional(data.surfaceToShare),
    city: optional(data.city),
    altitude: optional(data.altitude),
    // The API exposes its workflow's marking: the front only knows a status.
    status: oneOf(AREA_PROPOSAL_STATUSES, data.place),
    activityCodes: data.activities ?? [],
    proposer: toPersonName(data.proposerFirstName, data.proposerLastName),
    createdAt: toDate(data.createdAt),
    archivedAt: toDate(data.archivedAt),
  };
}
