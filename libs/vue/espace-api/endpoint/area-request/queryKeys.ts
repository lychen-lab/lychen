import type { ApiAreaRequestsGetCollectionData } from '@lychen/typescript-espace-api-sdk/generated/espace-api';
import { createResourceQueryKeys } from '@lychen/vue-api-query/queryKeys';

export const areaRequestKeys = createResourceQueryKeys<ApiAreaRequestsGetCollectionData['query']>(
  'espace',
  'area-request',
);
