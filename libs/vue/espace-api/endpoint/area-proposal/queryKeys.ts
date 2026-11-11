import type { AreaProposalFilters } from '@lychen/typescript-espace/models/AreaProposal';

const ALL = ['espace', 'area-proposals'] as const;

/**
 * The cache keys of the area proposal queries, from the widest to the narrowest, so that
 * a single call reaches a whole family: `invalidateQueries({ queryKey: lists() })`
 * refetches every list, whatever its filters.
 */
export const areaProposalKeys = {
  all: ALL,
  lists: () => [...ALL, 'list'] as const,
  list: (filters: AreaProposalFilters) => [...ALL, 'list', filters] as const,
  details: () => [...ALL, 'detail'] as const,
  detail: (uuid: string) => [...ALL, 'detail', uuid] as const,
};
