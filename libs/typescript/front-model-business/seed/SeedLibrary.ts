/**
 * A place that keeps seed lots and lends them out. Every field is optional: an endpoint says
 * which ones it gives, a component which ones it needs (`WithRequired`).
 */
export interface SeedLibrary {
  id?: string;
  name?: string;
  city?: string;
  /** Its lots: a lot moves to another library from the lot itself. */
  seedLotIds?: string[];
  createdAt?: Date;
  updatedAt?: Date;
}

/** What the creation form gives. */
export interface SeedLibraryCreateInput {
  name: NonNullable<SeedLibrary['name']>;
  city?: SeedLibrary['city'];
}

/** The fields to change; those left out are kept. */
export type SeedLibraryUpdateInput = Partial<SeedLibraryCreateInput>;
