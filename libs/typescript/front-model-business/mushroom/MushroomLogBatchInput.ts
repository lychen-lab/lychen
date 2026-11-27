import type { WithRequired } from '../WithRequired';
import type { InoculationInput } from './Inoculation';
import type { MushroomLog } from './MushroomLog';

/**
 * What the batch form gives: "create B-001 to B-040 in this zone, in oak, inoculated on that
 * day with that strain". The labels are the prefix, then the numbers from `startNumber`,
 * `count` of them, each padded with zeros to `digits` digits (`B-`, 1, 40, 3 gives `B-001`
 * to `B-040`).
 */
export interface MushroomLogBatchInput {
  prefix: string;
  startNumber: number;
  count: number;
  digits: number;
  /** Only its `id` is sent. */
  zone: WithRequired<NonNullable<MushroomLog['zone']>, 'id'>;
  /** Only its `id` is sent. */
  woodSpecies?: WithRequired<NonNullable<MushroomLog['woodSpecies']>, 'id'>;
  /** The inoculation every log of the batch gets, when they were all inoculated together. */
  inoculation?: Omit<InoculationInput, 'mushroomLog'>;
}
