/** Where a seed lot stands: free to reserve, reserved by someone, or given away. */
export const SEED_LOT_STATUSES = ['available', 'reserved', 'given'] as const;

export type SeedLotStatus = (typeof SEED_LOT_STATUSES)[number];

/** A lot of seeds a library keeps. */
export interface SeedLot {
  uuid: string;
  variety: string;
  harvestYear?: number;
  quantityInGrams?: number;
  /** The library keeping it. */
  seedLibraryUuid?: string;
  characteristicUuids: string[];
  /** Undefined for a status this front does not know yet. */
  status?: SeedLotStatus;
  createdAt?: Date;
  updatedAt?: Date;
}
