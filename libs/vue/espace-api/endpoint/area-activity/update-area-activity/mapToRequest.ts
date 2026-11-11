import type { ApiAreaActivitiesUuidPatchData } from '@lychen/typescript-espace-api-sdk/generated/espace-api';
import { withoutUndefined } from '@lychen/typescript-api-mapping/mapping';
import type { AreaActivityUpdateInput } from '@lychen/typescript-espace-models/AreaActivity';

export interface UpdateAreaActivityVariables {
  uuid: string;
  /** The fields to change; those left out are kept. */
  input: AreaActivityUpdateInput;
}

/** The request, from the front's input: a JSON Merge Patch of the fields set only. */
export function mapToRequest({
  uuid,
  input,
}: UpdateAreaActivityVariables): Omit<ApiAreaActivitiesUuidPatchData, 'url'> {
  return {
    path: { uuid },
    body: withoutUndefined({
      code: input.code,
    }),
  };
}
