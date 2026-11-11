/**
 * A page of a list, as API Platform's Hydra collections give it (`member`, `totalItems`,
 * `view`), each member mapped to the front's model. Only the JSON-LD envelope (`@context`,
 * `@id`, `@type`) and the `search` template of the filters are left out.
 */
export interface Collection<Item> {
  member: Item[];
  /** Across every page. The API leaves it out when it paginates without counting. */
  totalItems?: number;
  /** The links between pages, when the API gives them. */
  view?: CollectionView;
}

/** The URLs of a collection's pages, as its `view` gives them. */
export interface CollectionView {
  first?: string;
  last?: string;
  previous?: string;
  next?: string;
}
