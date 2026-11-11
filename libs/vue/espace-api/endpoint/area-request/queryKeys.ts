import type { AreaRequestFilters } from '@lychen/typescript-espace/models/AreaRequest';

const ALL = ['espace', 'area-requests'] as const;

/**
 * The cache keys of the area request queries, from the widest to the narrowest, so that
 * a single call reaches a whole family: `invalidateQueries({ queryKey: lists() })`
 * refetches every list, whatever its filters.
 */
export const areaRequestKeys = {
  all: ALL,
  lists: () => [...ALL, 'list'] as const,
  list: (filters: AreaRequestFilters) => [...ALL, 'list', filters] as const,
  details: () => [...ALL, 'detail'] as const,
  detail: (uuid: string) => [...ALL, 'detail', uuid] as const,
};
