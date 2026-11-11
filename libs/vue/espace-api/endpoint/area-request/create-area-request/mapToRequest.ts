import type { ApiAreaRequestsPostData } from '@lychen/typescript-espace-api-sdk/generated/espace-api';
import { withoutUndefined } from '@lychen/typescript-api-mapping/mapping';
import type { AreaRequestCreateInput } from '@lychen/typescript-front-model-business/area/AreaRequest';

/** The request, from the front's input, under the API's names. */
export function mapToRequest(input: AreaRequestCreateInput): Omit<ApiAreaRequestsPostData, 'url'> {
  return {
    body: withoutUndefined({
      uuid: input.id,
      title: input.title,
      description: input.description,
      minimalSurfaceRequested: input.minimalSurfaceRequested,
      city: input.city,
      activities: input.activityCodes ?? [],
    }),
  };
}
