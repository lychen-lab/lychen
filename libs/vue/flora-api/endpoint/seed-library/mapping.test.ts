import type { ApiSeedLibrariesUuidGetResponse } from '@lychen/typescript-flora-api-sdk/generated/flora-api';
import { describe, expect, it, vi } from 'vitest';
import { mapToRequest as mapToCreateRequest } from './useCreateSeedLibrary';
import { mapToModel } from './useGetSeedLibrary';
import { mapToRequest as mapToUpdateRequest } from './useUpdateSeedLibrary';

// Each endpoint composable maps on its own: these are the seed library ones. Their mapping
// functions need no client: stub it, as the real one signs in through Zitadel.
vi.mock('../../client/useFloraApi', () => ({ useFloraApi: () => ({ api: {} }) }));

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

describe('seed library endpoints mapping', () => {
  it('maps a library to the front model, under the front names', () => {
    expect(mapToModel(apiLibrary())).toStrictEqual({
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

  it('builds the writes from the fields set only', () => {
    expect(mapToCreateRequest({ name: 'Grainothèque' })).toEqual({
      body: { name: 'Grainothèque' },
    });
    expect(mapToUpdateRequest({ uuid: UUID, input: { city: 'Lyon' } })).toEqual({
      path: { uuid: UUID },
      body: { city: 'Lyon' },
    });
  });
});
