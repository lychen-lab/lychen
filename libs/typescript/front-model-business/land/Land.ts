/**
 * A land someone gardens or manages. Every field is optional: an endpoint says which ones it
 * gives, a component which ones it needs (`WithRequired`). Its members, areas and settings are
 * other concepts, which a screen fetches by their own endpoints when it needs them.
 */
export interface Land {
  id?: string;
  name?: string;
  /** In m². */
  surface?: number;
  /** In metres. */
  altitude?: number;
  createdAt?: Date;
  updatedAt?: Date;
}

/** What the creation form gives. */
export interface LandCreateInput {
  name: NonNullable<Land['name']>;
  surface?: Land['surface'];
  altitude?: Land['altitude'];
}

/** The fields to change; those left out are kept. */
export type LandUpdateInput = Partial<LandCreateInput>;
