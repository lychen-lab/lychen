import type { ApiLandsUlidGetResponse } from '@lychen/typescript-tera-api-sdk/generated/tera-api';
import { describe, expect, it } from 'vitest';
import { mapToCreateBody, mapToModel, mapToUpdateBody } from './mapper';

const ULID = '01JD0R1X7Q8ZK3V5N2M4P6T9WB';

function apiLand(overrides: Partial<ApiLandsUlidGetResponse> = {}): ApiLandsUlidGetResponse {
  return {
    '@id': `/api/lands/${ULID}`,
    '@type': 'Land',
    ulid: ULID,
    name: 'Potager du Moulin',
    surface: 450,
    altitude: 210,
    landMembers: [`/api/land_members/01JD0R2A`],
    landAreas: [],
    landSetting: `/api/land_settings/01JD0R2B`,
    createdAt: '2026-11-01T08:00:00+00:00',
    updatedAt: null,
    ...overrides,
  };
}

describe('land mapper', () => {
  it('maps a land to the front model, without the JSON-LD envelope nor the IRIs', () => {
    expect(mapToModel(apiLand())).toEqual({
      ulid: ULID,
      name: 'Potager du Moulin',
      surface: 450,
      altitude: 210,
      createdAt: new Date('2026-11-01T08:00:00Z'),
      updatedAt: undefined,
    });
  });

  it('turns the API nulls into undefined', () => {
    const land = mapToModel(apiLand({ surface: null, altitude: null }));
    expect(land.surface).toBeUndefined();
    expect(land.altitude).toBeUndefined();
  });

  it('rejects a land without a ulid', () => {
    expect(() => mapToModel(apiLand({ ulid: null }))).toThrow('land ulid');
  });

  it('builds the bodies from the fields set only', () => {
    expect(mapToCreateBody({ name: 'Potager', surface: 100 })).toEqual({
      name: 'Potager',
      surface: 100,
    });
    expect(mapToUpdateBody({ altitude: 300 })).toEqual({ altitude: 300 });
  });
});
