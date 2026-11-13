import type { ApiAreaActivitiesUuidPatchResponse } from '@lychen/typescript-espace-api-sdk/generated/espace-api';
import { required, toDate } from '@lychen/typescript-api-mapping/mapping';
import type { AreaActivity } from '@lychen/typescript-front-model-business/area/AreaActivity';
import type { WithRequired } from '@lychen/typescript-front-model-business/WithRequired';

/** The fields of the area activity this endpoint always gives. */
export type Model = WithRequired<AreaActivity, 'id'>;

/** The changed area activity as the front's model. */
export function mapToModel(data: ApiAreaActivitiesUuidPatchResponse): Model {
  return {
    id: required(data.uuid, 'area activity uuid'),
    // The API leaves the code out of a single activity.
    createdAt: toDate(data.createdAt),
  };
}
