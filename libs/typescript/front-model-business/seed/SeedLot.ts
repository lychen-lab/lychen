import { type ObjectValues } from '@lychen/typescript-utils/transformers/ObjectValues';

/** Where a seed lot stands: free to reserve, reserved by someone, or given away. */
export const SEED_LOT_STATE = {
  Available: 'available',
  Reserved: 'reserved',
  Given: 'given',
} as const;

export type SeedLotState = ObjectValues<typeof SEED_LOT_STATE>;

/**
 * A lot of seeds a library keeps. Every field is optional: an endpoint says which ones it
 * gives, a component which ones it needs (`WithRequired`).
 */
export interface SeedLot {
  id?: string;
  variety?: string;
  harvestYear?: number;
  quantityInGrams?: number;
  /** The library keeping it. */
  seedLibraryId?: string;
  characteristicIds?: string[];
  /** Undefined for a state this front does not know yet. */
  state?: SeedLotState;
  createdAt?: Date;
  updatedAt?: Date;
}
