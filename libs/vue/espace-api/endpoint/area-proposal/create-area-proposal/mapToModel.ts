import type { ApiAreaProposalsPostResponse } from '@lychen/typescript-espace-api-sdk/generated/espace-api';
import { oneOf, optional, required, toDate } from '@lychen/typescript-api-mapping/mapping';
import {
  AREA_PROPOSAL_STATUS,
  type AreaProposal,
} from '@lychen/typescript-front-model-business/area/AreaProposal';
import type { WithRequired } from '@lychen/typescript-front-model-business/WithRequired';
import { toPersonName } from '../../person';

/** The fields of the area proposal this endpoint always gives. */
export type Model = WithRequired<AreaProposal, 'id' | 'title' | 'description' | 'activityCodes'>;

/** The created area proposal as the front's model. */
export function mapToModel(data: ApiAreaProposalsPostResponse): Model {
  return {
    id: required(data.uuid, 'area proposal uuid'),
    title: data.title ?? '',
    description: data.description ?? '',
    surfaceTotal: optional(data.surfaceTotal),
    surfaceToShare: optional(data.surfaceToShare),
    city: optional(data.city),
    altitude: optional(data.altitude),
    // The API exposes its workflow's marking: the front only knows a status.
    status: oneOf(AREA_PROPOSAL_STATUS, data.place),
    activityCodes: data.activities ?? [],
    proposer: toPersonName(data.proposerFirstName, data.proposerLastName),
    createdAt: toDate(data.createdAt),
    archivedAt: toDate(data.archivedAt),
  };
}
