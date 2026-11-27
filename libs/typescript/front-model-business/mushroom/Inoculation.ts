import { type ObjectValues } from '@lychen/typescript-utils/transformers/ObjectValues';
import type { WithRequired } from '../WithRequired';
import type { MushroomLog } from './MushroomLog';
import type { Strain } from './Strain';

/** How the mushroom was put in the log. */
export const INOCULATION_METHOD = {
  Dowels: 'dowels',
  Sawdust: 'sawdust',
  Other: 'other',
} as const;

export type InoculationMethod = ObjectValues<typeof INOCULATION_METHOD>;

/**
 * The inoculation of a mushroom log with a strain: one per log. Every field is optional: an
 * endpoint says which ones it gives, a component which ones it needs (`WithRequired`).
 */
export interface Inoculation {
  id?: string;
  mushroomLog?: MushroomLog;
  strain?: Strain;
  /** A date in the past is fine: logs inoculated months ago are entered too. */
  inoculatedAt?: Date;
  /** Undefined for a method this front does not know yet. */
  method?: InoculationMethod;
  /** For instance the number of dowels. */
  quantity?: number;
  notes?: string;
  createdAt?: Date;
  updatedAt?: Date;
}

/** What the inoculation form gives. */
export interface InoculationInput {
  /** Only its `id` is sent. */
  mushroomLog: WithRequired<NonNullable<Inoculation['mushroomLog']>, 'id'>;
  /** Only its `id` is sent. */
  strain: WithRequired<NonNullable<Inoculation['strain']>, 'id'>;
  inoculatedAt: NonNullable<Inoculation['inoculatedAt']>;
  method: NonNullable<Inoculation['method']>;
  quantity?: Inoculation['quantity'];
  notes?: Inoculation['notes'];
}
