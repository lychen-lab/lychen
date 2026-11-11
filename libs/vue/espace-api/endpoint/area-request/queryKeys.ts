import type { AreaRequestFilters } from '@lychen/typescript-espace-models/AreaRequest';
import { createQueryKeys } from '@lychen/vue-api-query/queryKeys';

export const areaRequestKeys = createQueryKeys<AreaRequestFilters>('espace', 'area-requests');
