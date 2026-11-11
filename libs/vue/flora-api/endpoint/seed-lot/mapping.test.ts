import type { ApiSeedLotsUuidgivePatchResponse } from '@lychen/typescript-flora-api-sdk/generated/flora-api';
import { describe, expect, it, vi } from 'vitest';
import { mapToModel as mapGiven, mapToRequest as give } from './useGiveSeedLot';
import { mapToRequest as release } from './useReleaseSeedLot';
import { mapToRequest as reserve } from './useReserveSeedLot';

vi.mock('../../client/useFloraApi', () => ({ useFloraApi: () => ({ api: {} }) }));

const UUID = '0198b2c3-d4e5-7f60-8a9b-ffffffffffff';
const LIBRARY = '0198b2c3-d4e5-7f60-8a9b-1c2d3e4f5a6b';

describe('the seed lot workflow transitions', () => {
  it('send no body, to the transition of the lot', () => {
    for (const transition of [reserve, release, give]) {
      expect(transition(UUID)).toStrictEqual({ path: { uuid: UUID } });
    }
  });

  it('give the lot as the front model, its workflow place as a status', () => {
    const response: ApiSeedLotsUuidgivePatchResponse = {
      '@id': `/api/seed_lots/${UUID}`,
      '@type': 'SeedLot',
      uuid: UUID,
      variety: 'Tomate cœur de bœuf',
      harvestYear: 2026,
      quantityInGrams: 12,
      seedLibrary: LIBRARY,
      characteristics: [],
      state: 'given',
      createdAt: '2026-11-01T08:00:00+00:00',
      updatedAt: '2026-11-10T08:00:00+00:00',
    };
    expect(mapGiven(response)).toStrictEqual({
      uuid: UUID,
      variety: 'Tomate cœur de bœuf',
      harvestYear: 2026,
      quantityInGrams: 12,
      seedLibraryUuid: LIBRARY,
      characteristicUuids: [],
      status: 'given',
      createdAt: new Date('2026-11-01T08:00:00Z'),
      updatedAt: new Date('2026-11-10T08:00:00Z'),
    });
    expect(mapGiven({ ...response, state: 'lost' }).status).toBeUndefined();
  });
});
