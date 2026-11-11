import type { ApiAreaActivitiesPostData } from '@lychen/typescript-espace-api-sdk/generated/espace-api';
import { withoutUndefined } from '@lychen/typescript-api-mapping/mapping';
import type { AreaActivityCreateInput } from '@lychen/typescript-espace-models/AreaActivity';

/** The request, from the front's input, under the API's names. */
export function mapToRequest(
  input: AreaActivityCreateInput,
): Omit<ApiAreaActivitiesPostData, 'url'> {
  return {
    body: withoutUndefined({
      uuid: input.uuid,
      code: input.code,
    }),
  };
}
