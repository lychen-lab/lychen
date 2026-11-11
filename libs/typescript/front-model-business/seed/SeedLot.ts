import { type ObjectValues } from '@lychen/typescript-utils/transformers/ObjectValues';

/** Where a seed lot stands: free to reserve, reserved by someone, or given away. */
export const SEED_LOT_STATUS = {
  Available: 'available',
  Reserved: 'reserved',
  Given: 'given',
} as const;

export type SeedLotStatus = ObjectValues<typeof SEED_LOT_STATUS>;

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
  /** Undefined for a status this front does not know yet. */
  status?: SeedLotStatus;
  createdAt?: Date;
  updatedAt?: Date;
}
