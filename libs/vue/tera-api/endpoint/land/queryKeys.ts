import type { LandFilters } from '@lychen/typescript-tera-models/Land';
import { createQueryKeys } from '@lychen/vue-api-query/queryKeys';

export const landKeys = createQueryKeys<LandFilters>('tera', 'lands');
