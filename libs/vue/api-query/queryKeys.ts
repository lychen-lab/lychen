/**
 * The cache keys of a resource of an API, from the widest to the narrowest, so that one call
 * reaches a whole family: `invalidateQueries({ queryKey: lists() })` refetches every page,
 * whatever its filters. Every resource identifies its items by one string id (its concept's
 * `id`, a uuid or a ulid), so the same keys fit them all; a read of an item's own (its workflow diagram, say) goes under
 * its key: `[...detail(id), 'workflow']`, and goes stale with it.
 *
 *   export const areaProposalKeys = createResourceQueryKeys<
 *     ApiAreaProposalsGetCollectionData['query']
 *   >('espace', 'area-proposal');
 *
 * The API's name comes first, so that APIs sharing the app's QueryClient never collide.
 */
export function createResourceQueryKeys<TFilters>(api: string, resource: string) {
  const all = [api, resource] as const;
  return {
    /** Every query of the resource. */
    all,
    /** Every page of the resource's lists, whatever its filters. */
    lists: () => [...all, 'list'] as const,
    /** The page these filters select. */
    list: (filters: TFilters) => [...all, 'list', filters] as const,
    /** Every item of the resource. */
    details: () => [...all, 'detail'] as const,
    /** The item with this id, and whatever is read of it. */
    detail: (id: string) => [...all, 'detail', id] as const,
  };
}
