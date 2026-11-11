import type { AreaActivityFilters } from '@lychen/typescript-espace-models/AreaActivity';
import { createQueryKeys } from '@lychen/vue-api-query/queryKeys';

export const areaActivityKeys = createQueryKeys<AreaActivityFilters>('espace', 'area-activities');
