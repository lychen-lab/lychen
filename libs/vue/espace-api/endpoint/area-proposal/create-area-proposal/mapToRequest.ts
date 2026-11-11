import type { ApiAreaProposalsPostData } from '@lychen/typescript-espace-api-sdk/generated/espace-api';
import { withoutUndefined } from '@lychen/typescript-api-mapping/mapping';
import type { AreaProposalCreateInput } from '@lychen/typescript-espace-models/AreaProposal';

/** The request, from the front's input, under the API's names. */
export function mapToRequest(
  input: AreaProposalCreateInput,
): Omit<ApiAreaProposalsPostData, 'url'> {
  return {
    body: withoutUndefined({
      uuid: input.uuid,
      title: input.title,
      description: input.description,
      surfaceTotal: input.surfaceTotal,
      surfaceToShare: input.surfaceToShare,
      city: input.city,
      altitude: input.altitude,
      activities: input.activityCodes ?? [],
    }),
  };
}
