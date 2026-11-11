import type { ApiAreaActivitiesUuidPatchData } from '@lychen/typescript-espace-api-sdk/generated/espace-api';
import { withoutUndefined } from '@lychen/typescript-api-mapping/mapping';
import type { AreaActivityUpdateInput } from '@lychen/typescript-front-model-business/area/AreaActivity';

export interface UpdateAreaActivityVariables {
  id: string;
  /** The fields to change; those left out are kept. */
  input: AreaActivityUpdateInput;
}

/** The request, from the front's input: a JSON Merge Patch of the fields set only. */
export function mapToRequest({
  id,
  input,
}: UpdateAreaActivityVariables): Omit<ApiAreaActivitiesUuidPatchData, 'url'> {
  return {
    path: { uuid: id },
    body: withoutUndefined({
      code: input.code,
    }),
  };
}
