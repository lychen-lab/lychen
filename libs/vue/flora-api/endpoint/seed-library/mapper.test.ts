import type { ApiSeedLibrariesUuidGetResponse } from '@lychen/typescript-flora-api-sdk/generated/flora-api';
import { describe, expect, it } from 'vitest';
import { mapToCreateBody, mapToModel, mapToUpdateBody } from './mapper';

const UUID = '0198b2c3-d4e5-7f60-8a9b-1c2d3e4f5a6b';
const LOT = '0198b2c3-d4e5-7f60-8a9b-ffffffffffff';

function apiLibrary(
  overrides: Partial<ApiSeedLibrariesUuidGetResponse> = {},
): ApiSeedLibrariesUuidGetResponse {
  return {
    '@id': `/api/seed_libraries/${UUID}`,
    '@type': 'SeedLibrary',
    uuid: UUID,
    name: 'Grainothèque du quartier',
    city: 'Grenoble',
    seedLots: [LOT],
    createdAt: '2026-11-01T08:00:00+00:00',
    updatedAt: null,
    ...overrides,
  };
}

describe('seed library mapper', () => {
  it('maps a library to the front model, under the front names', () => {
    expect(mapToModel(apiLibrary())).toEqual({
      uuid: UUID,
      name: 'Grainothèque du quartier',
      city: 'Grenoble',
      seedLotUuids: [LOT],
      createdAt: new Date('2026-11-01T08:00:00Z'),
      updatedAt: undefined,
    });
  });

  it('turns the API nulls into undefined, and missing lots into none', () => {
    const library = mapToModel(apiLibrary({ city: null, seedLots: undefined }));
    expect(library.city).toBeUndefined();
    expect(library.seedLotUuids).toEqual([]);
  });

  it('builds the bodies from the fields set only', () => {
    expect(mapToCreateBody({ name: 'Grainothèque' })).toEqual({ name: 'Grainothèque' });
    expect(mapToUpdateBody({ city: 'Lyon' })).toEqual({ city: 'Lyon' });
  });
});
