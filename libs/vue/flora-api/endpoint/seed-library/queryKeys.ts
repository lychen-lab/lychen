import type { ApiSeedLibrariesGetCollectionData } from '@lychen/typescript-flora-api-sdk/generated/flora-api';
import { createResourceQueryKeys } from '@lychen/vue-api-query/queryKeys';

export const seedLibraryKeys = createResourceQueryKeys<ApiSeedLibrariesGetCollectionData['query']>(
  'flora',
  'seed-library',
);
