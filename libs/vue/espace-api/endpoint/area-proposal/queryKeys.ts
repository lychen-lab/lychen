import type { ApiAreaProposalsGetCollectionData } from '@lychen/typescript-espace-api-sdk/generated/espace-api';
import { createResourceQueryKeys } from '@lychen/vue-api-query/queryKeys';

export const areaProposalKeys = createResourceQueryKeys<ApiAreaProposalsGetCollectionData['query']>(
  'espace',
  'area-proposal',
);
