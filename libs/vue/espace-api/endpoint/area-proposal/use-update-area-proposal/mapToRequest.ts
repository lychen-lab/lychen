import type { ApiAreaProposalsUuidPatchData } from '@lychen/typescript-espace-api-sdk/generated/espace-api';
import { withoutUndefined } from '@lychen/typescript-api-mapping/mapping';
import type { AreaProposalUpdateInput } from '@lychen/typescript-espace-models/AreaProposal';

export interface UpdateAreaProposalVariables {
  uuid: string;
  /** The fields to change; those left out are kept. */
  input: AreaProposalUpdateInput;
}

/** The request, from the front's input: a JSON Merge Patch of the fields set only. */
export function mapToRequest({
  uuid,
  input,
}: UpdateAreaProposalVariables): Omit<ApiAreaProposalsUuidPatchData, 'url'> {
  return {
    path: { uuid },
    body: withoutUndefined({
      title: input.title,
      description: input.description,
      surfaceTotal: input.surfaceTotal,
      surfaceToShare: input.surfaceToShare,
      city: input.city,
      altitude: input.altitude,
      activities: input.activityCodes,
    }),
  };
}
