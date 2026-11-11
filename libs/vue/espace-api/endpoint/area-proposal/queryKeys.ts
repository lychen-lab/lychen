import type { AreaProposalFilters } from '@lychen/typescript-espace-models/AreaProposal';
import { createQueryKeys } from '@lychen/vue-api-query/queryKeys';

export const areaProposalKeys = createQueryKeys<AreaProposalFilters>('espace', 'area-proposals');
