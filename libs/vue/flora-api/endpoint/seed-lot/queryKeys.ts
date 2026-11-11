import type { ApiSeedLotsGetCollectionData } from '@lychen/typescript-flora-api-sdk/generated/flora-api';
import { createResourceQueryKeys } from '@lychen/vue-api-query/queryKeys';

export const seedLotKeys = createResourceQueryKeys<ApiSeedLotsGetCollectionData['query']>(
  'flora',
  'seed-lot',
);
