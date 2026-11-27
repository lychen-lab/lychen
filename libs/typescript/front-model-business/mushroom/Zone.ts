/**
 * A place where mushrooms grow: undergrowth, cellar, greenhouse, yard. Every field is optional:
 * an endpoint says which ones it gives, a component which ones it needs (`WithRequired`).
 */
export interface Zone {
  id?: string;
  name?: string;
  description?: string;
  createdAt?: Date;
  updatedAt?: Date;
}

/** What the zone form gives. */
export interface ZoneInput {
  name: NonNullable<Zone['name']>;
  description?: Zone['description'];
}
