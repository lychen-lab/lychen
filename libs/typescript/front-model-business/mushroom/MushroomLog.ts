import { type ObjectValues } from '@lychen/typescript-utils/transformers/ObjectValues';
import type { WithRequired } from '../WithRequired';
import type { Inoculation } from './Inoculation';
import type { WoodSpecies } from './WoodSpecies';
import type { Zone } from './Zone';

/** Where a mushroom log stands in its life. */
export const MUSHROOM_LOG_STATE = {
  Prepared: 'prepared',
  Incubating: 'incubating',
  Fruiting: 'fruiting',
  Retired: 'retired',
} as const;

export type MushroomLogState = ObjectValues<typeof MUSHROOM_LOG_STATE>;

/**
 * A log of wood (a bûche) mushrooms grow on. Every field is optional: an endpoint says which
 * ones it gives, a component which ones it needs (`WithRequired`). Its journal and harvests
 * are other concepts, which a screen fetches by their own endpoints.
 */
export interface MushroomLog {
  id?: string;
  /** What is written on the log, such as `B-012`: unique for a person. */
  label?: string;
  zone?: Zone;
  woodSpecies?: WoodSpecies;
  /** The day the tree was cut. */
  cutAt?: Date;
  /** In centimetres. */
  diameterCm?: number;
  /** In centimetres. */
  lengthCm?: number;
  notes?: string;
  /** Undefined for a state this front does not know yet. */
  state?: MushroomLogState;
  inoculation?: Inoculation;
  createdAt?: Date;
  updatedAt?: Date;
}

/**
 * What the log form gives. The state is not part of it: a log is created `prepared` and the
 * lifecycle moves it.
 */
export interface MushroomLogInput {
  /** Only its `id` is sent. */
  zone: WithRequired<NonNullable<MushroomLog['zone']>, 'id'>;
  label: NonNullable<MushroomLog['label']>;
  /** Only its `id` is sent. */
  woodSpecies?: WithRequired<NonNullable<MushroomLog['woodSpecies']>, 'id'>;
  cutAt?: MushroomLog['cutAt'];
  diameterCm?: MushroomLog['diameterCm'];
  lengthCm?: MushroomLog['lengthCm'];
  notes?: MushroomLog['notes'];
}
