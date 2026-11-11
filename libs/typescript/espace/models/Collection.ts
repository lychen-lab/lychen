/** One page of a list, whatever paginates it on the API side. */
export interface Collection<Item> {
  items: Item[];
  /** Across every page. */
  totalItems: number;
  hasNextPage: boolean;
}
