/** A land, as a list shows it. */
export interface LandSummary {
  ulid: string;
  name: string;
  /** In m². */
  surface?: number;
  /** In metres. */
  altitude?: number;
  createdAt?: Date;
  updatedAt?: Date;
}

/**
 * A land, with everything its own page shows. The API sends the same fields as for a list:
 * its members, areas and settings are other resources, which a screen fetches by their own
 * composables when it needs them.
 */
export interface Land extends LandSummary {}

/** What the creation form gives. */
export interface LandCreateInput {
  name: string;
  /** In m². */
  surface?: number;
  /** In metres. */
  altitude?: number;
}

/** The fields to change; those left out are kept. */
export type LandUpdateInput = Partial<LandCreateInput>;

export interface LandFilters {
  page?: number;
  itemsPerPage?: number;
}
