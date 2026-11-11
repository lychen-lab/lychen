import type { ApiAreaProposalsGetCollectionResponse } from '@lychen/typescript-espace-api-sdk/generated/espace-api';
import type { Collection } from '@lychen/typescript-api-mapping/Collection';
import { oneOf, optional, required, toCollection } from '@lychen/typescript-api-mapping/mapping';
import {
  AREA_PROPOSAL_STATUSES,
  type AreaProposalSummary,
} from '@lychen/typescript-espace-models/AreaProposal';
import { toPersonName } from '../../person';

/** The page this endpoint returns, as API Platform gives it, its members as the front's model. */
export function mapToModel(
  data: ApiAreaProposalsGetCollectionResponse,
): Collection<AreaProposalSummary> {
  return toCollection(data, mapToMember);
}

function mapToMember(
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
