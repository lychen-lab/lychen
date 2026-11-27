/**
 * A species of mushroom (shiitake, oyster mushroom…): common reference data, read-only. Its
 * `code` (a slug such as `shiitake`) is what the app translates. Every field is optional: an
 * endpoint says which ones it gives, a component which ones it needs (`WithRequired`).
 */
export interface FungusSpecies {
  id?: string;
  /** A unique slug, such as `shiitake`. */
  code?: string;
  /** Such as `Lentinula edodes`. */
  scientificName?: string;
}
