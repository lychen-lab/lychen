import type { ApiAreaRequestsUuidPatchData } from '@lychen/typescript-espace-api-sdk/generated/espace-api';
import { withoutUndefined } from '@lychen/typescript-api-mapping/mapping';
import type { AreaRequestUpdateInput } from '@lychen/typescript-front-model-business/area/AreaRequest';

export interface UpdateAreaRequestVariables {
  id: string;
  /** The fields to change; those left out are kept. */
  input: AreaRequestUpdateInput;
}

/** The request, from the front's input: a JSON Merge Patch of the fields set only. */
export function mapToRequest({
  id,
  input,
}: UpdateAreaRequestVariables): Omit<ApiAreaRequestsUuidPatchData, 'url'> {
  return {
    path: { uuid: id },
    body: withoutUndefined({
      title: input.title,
      description: input.description,
      minimalSurfaceRequested: input.minimalSurfaceRequested,
      city: input.city,
      activities: input.activityCodes,
    }),
  };
}
