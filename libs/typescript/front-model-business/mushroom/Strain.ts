import type { WithRequired } from '../WithRequired';
import type { FungusSpecies } from './FungusSpecies';

/**
 * A strain of a species, from a person's own catalogue (`M3782`, from such a supplier). Every
 * field is optional: an endpoint says which ones it gives, a component which ones it needs
 * (`WithRequired`).
 */
export interface Strain {
  id?: string;
  /** The name or reference of the strain, such as `M3782`. */
  name?: string;
  supplier?: string;
  notes?: string;
  fungusSpecies?: FungusSpecies;
  createdAt?: Date;
  updatedAt?: Date;
}

/** What the strain form gives. */
export interface StrainInput {
  /** Only its `id` is sent. */
  fungusSpecies: WithRequired<NonNullable<Strain['fungusSpecies']>, 'id'>;
  name: NonNullable<Strain['name']>;
  supplier?: Strain['supplier'];
  notes?: Strain['notes'];
}
