import type { ApiLandsGetCollectionData } from '@lychen/typescript-tera-api-sdk/generated/tera-api';
import { createResourceQueryKeys } from '@lychen/vue-api-query/queryKeys';

export const landKeys = createResourceQueryKeys<ApiLandsGetCollectionData['query']>('tera', 'land');
