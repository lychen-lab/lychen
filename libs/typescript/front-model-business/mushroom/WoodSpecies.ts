/**
 * A species of wood (oak, beech…): common reference data, read-only. Its `code` (a slug such
 * as `oak`) is what the app translates. Every field is optional: an endpoint says which ones
 * it gives, a component which ones it needs (`WithRequired`).
 */
export interface WoodSpecies {
  id?: string;
  /** A unique slug, such as `oak`. */
  code?: string;
  /** Such as `Quercus`. */
  scientificName?: string;
}
