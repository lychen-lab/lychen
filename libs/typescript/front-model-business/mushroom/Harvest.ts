import type { WithRequired } from '../WithRequired';
import type { MushroomLog } from './MushroomLog';

/**
 * A weighed harvest of a mushroom log. Every field is optional: an endpoint says which ones it
 * gives, a component which ones it needs (`WithRequired`).
 */
export interface Harvest {
  id?: string;
  mushroomLog?: MushroomLog;
  /** A day, not in the future. */
  harvestedAt?: Date;
  /** In grams, more than 0. */
  weightGrams?: number;
  notes?: string;
  createdAt?: Date;
}

/** What the harvest form gives. */
export interface HarvestInput {
  /** Only its `id` is sent. */
  mushroomLog: WithRequired<NonNullable<Harvest['mushroomLog']>, 'id'>;
  harvestedAt: NonNullable<Harvest['harvestedAt']>;
  weightGrams: NonNullable<Harvest['weightGrams']>;
  notes?: Harvest['notes'];
}
