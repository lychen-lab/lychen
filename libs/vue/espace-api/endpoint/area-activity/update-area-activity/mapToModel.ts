import type { ApiAreaActivitiesUuidPatchResponse } from '@lychen/typescript-espace-api-sdk/generated/espace-api';
import { required, toDate } from '@lychen/typescript-api-mapping/mapping';
import type { AreaActivity } from '@lychen/typescript-espace-models/AreaActivity';

/** The changed area activity as the front's model. */
export function mapToModel(data: ApiAreaActivitiesUuidPatchResponse): AreaActivity {
  return {
    uuid: required(data.uuid, 'area activity uuid'),
    // The API leaves the code out of a single activity.
    createdAt: toDate(data.createdAt),
  };
}
