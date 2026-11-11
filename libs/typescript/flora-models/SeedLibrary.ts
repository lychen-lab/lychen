/** A seed library, as a list shows it. */
export interface SeedLibrarySummary {
  uuid: string;
  name: string;
  city?: string;
  /** Its lots: a lot moves to another library from the lot itself. */
  seedLotUuids: string[];
  createdAt?: Date;
  updatedAt?: Date;
}

/** A seed library, with everything its own page shows: the same as in a list. */
export interface SeedLibrary extends SeedLibrarySummary {}

/** What the creation form gives. */
export interface SeedLibraryCreateInput {
  name: string;
  city?: string;
}

/** The fields to change; those left out are kept. */
export type SeedLibraryUpdateInput = Partial<SeedLibraryCreateInput>;

export interface SeedLibraryFilters {
  page?: number;
  itemsPerPage?: number;
}
