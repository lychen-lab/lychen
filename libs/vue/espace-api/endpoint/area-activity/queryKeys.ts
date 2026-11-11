import type { ApiAreaActivitiesGetCollectionData } from '@lychen/typescript-espace-api-sdk/generated/espace-api';
import { createResourceQueryKeys } from '@lychen/vue-api-query/queryKeys';

export const areaActivityKeys = createResourceQueryKeys<
  ApiAreaActivitiesGetCollectionData['query']
>('espace', 'area-activity');
