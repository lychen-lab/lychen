import type { AreaActivityFilters } from '@lychen/typescript-espace/models/AreaActivity';

const ALL = ['espace', 'area-activities'] as const;

/**
 * The cache keys of the area activity queries, from the widest to the narrowest, so that
 * a single call reaches a whole family: `invalidateQueries({ queryKey: lists() })`
 * refetches every list, whatever its filters.
 */
export const areaActivityKeys = {
  all: ALL,
  lists: () => [...ALL, 'list'] as const,
  list: (filters: AreaActivityFilters) => [...ALL, 'list', filters] as const,
  details: () => [...ALL, 'detail'] as const,
  detail: (uuid: string) => [...ALL, 'detail', uuid] as const,
};
