import type { SeedLibraryFilters } from '@lychen/typescript-flora-models/SeedLibrary';
import { createQueryKeys } from '@lychen/vue-api-query/queryKeys';

export const seedLibraryKeys = createQueryKeys<SeedLibraryFilters>('flora', 'seed-libraries');
