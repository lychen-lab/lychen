import type { ApiLandProposalsGetCollectionData } from '@lychen/typescript-tera-api-sdk/generated/tera-api';
import { createResourceQueryKeys } from '@lychen/vue-api-query/queryKeys';

export const landProposalKeys = createResourceQueryKeys<ApiLandProposalsGetCollectionData['query']>(
  'tera',
  'land-proposal',
);
