/**
 * The cache keys of one resource's queries, from the widest to the narrowest, so that a
 * single call reaches a whole family: `invalidateQueries({ queryKey: lists() })` refetches
 * every list, whatever its filters.
 */
export interface ResourceQueryKeys<TFilters> {
  /** Every query of the resource. */
  all: readonly [string, string];
  lists(): readonly [string, string, 'list'];
  list(filters: TFilters): readonly [string, string, 'list', TFilters];
  details(): readonly [string, string, 'detail'];
  detail(id: string): readonly [string, string, 'detail', string];
}

/**
 * The cache keys of a resource of an API, e.g. `createQueryKeys<LandFilters>('tera', 'lands')`.
 * The API's name comes first, so that APIs sharing the app's QueryClient never collide.
 */
export function createQueryKeys<TFilters = Record<string, never>>(
  api: string,
  resource: string,
): ResourceQueryKeys<TFilters> {
  const all = [api, resource] as const;
  return {
    all,
    lists: () => [...all, 'list'] as const,
    list: (filters) => [...all, 'list', filters] as const,
    details: () => [...all, 'detail'] as const,
    detail: (id) => [...all, 'detail', id] as const,
  };
}
