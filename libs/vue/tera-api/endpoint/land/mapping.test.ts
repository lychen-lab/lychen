import type { ApiLandsUlidGetResponse } from '@lychen/typescript-tera-api-sdk/generated/tera-api';
import { describe, expect, it, vi } from 'vitest';
import { mapToRequest as mapToCreateRequest } from './useCreateLand';
import { mapToRequest as mapToDeleteRequest } from './useDeleteLand';
import { mapToModel, mapToRequest as mapToGetRequest } from './useGetLand';
import { mapToRequest as mapToListRequest } from './useGetLands';
import { mapToRequest as mapToUpdateRequest } from './useUpdateLand';

// Each endpoint composable maps on its own: these are the land ones. Their mapping functions
// need no client: stub it, as the real one signs in through Zitadel.
vi.mock('../../client/useTeraApi', () => ({ useTeraApi: () => ({ api: {} }) }));

const ULID = '01JD0R1X7Q8ZK3V5N2M4P6T9WB';

function apiLand(overrides: Partial<ApiLandsUlidGetResponse> = {}): ApiLandsUlidGetResponse {
  return {
    '@id': `/api/lands/${ULID}`,
    '@type': 'Land',
    ulid: ULID,
    name: 'Potager du Moulin',
    surface: 450,
    altitude: 210,
    landMembers: ['/api/land_members/01JD0R2A'],
    landAreas: [],
    landSetting: '/api/land_settings/01JD0R2B',
    createdAt: '2026-11-01T08:00:00+00:00',
    updatedAt: null,
    ...overrides,
  };
}

describe('land endpoints mapping', () => {
  it('maps a land to the front model, without the JSON-LD envelope nor the IRIs', () => {
    expect(mapToModel(apiLand())).toStrictEqual({
      ulid: ULID,
      name: 'Potager du Moulin',
      surface: 450,
      altitude: 210,
      createdAt: new Date('2026-11-01T08:00:00Z'),
      updatedAt: undefined,
    });
  });

  it('turns the API nulls into undefined, and rejects a land without a ulid', () => {
    const land = mapToModel(apiLand({ surface: null, altitude: null }));
    expect(land.surface).toBeUndefined();
    expect(land.altitude).toBeUndefined();
    expect(() => mapToModel(apiLand({ ulid: null }))).toThrow('land ulid');
  });

  it('builds each request from the front names', () => {
    expect(mapToGetRequest(ULID)).toEqual({ path: { ulid: ULID } });
    expect(mapToListRequest({ page: 2 })).toEqual({ query: { page: 2 } });
    expect(mapToCreateRequest({ name: 'Potager', surface: 100 })).toEqual({
      body: { name: 'Potager', surface: 100 },
    });
    expect(mapToUpdateRequest({ ulid: ULID, input: { altitude: 300 } })).toEqual({
      path: { ulid: ULID },
      body: { altitude: 300 },
    });
    expect(mapToDeleteRequest(ULID)).toEqual({ path: { ulid: ULID } });
  });
});
