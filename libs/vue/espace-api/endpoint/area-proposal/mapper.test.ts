import type { ApiAreaProposalsUuidGetResponse } from '@lychen/typescript-espace-api-sdk/generated/espace-api';
import { describe, expect, it } from 'vitest';
import { mapToCreateBody, mapToModel, mapToSummaryModel, mapToUpdateBody } from './mapper';

const UUID = '0198a1b2-c3d4-7e5f-8a9b-0c1d2e3f4a5b';

function apiProposal(
  overrides: Partial<ApiAreaProposalsUuidGetResponse> = {},
): ApiAreaProposalsUuidGetResponse {
  return {
    '@id': `/api/area_proposals/${UUID}`,
    '@type': 'AreaProposal',
    uuid: UUID,
    title: 'Terrain de culture',
    description: 'Grand terrain ensoleillé.',
    surfaceTotal: 300,
    surfaceToShare: 130,
    city: 'Lille',
    altitude: 678,
    place: 'published',
    activities: ['gardening'],
    proposerFirstName: 'Sophie',
    proposerLastName: 'Martin',
    createdAt: '2026-11-01T08:00:00+00:00',
    archivedAt: null,
    ...overrides,
  };
}

describe('area proposal mapper', () => {
  it('maps a proposal to the front model, under the front names', () => {
    expect(mapToModel(apiProposal())).toEqual({
      uuid: UUID,
      title: 'Terrain de culture',
      description: 'Grand terrain ensoleillé.',
      surfaceTotal: 300,
      surfaceToShare: 130,
      city: 'Lille',
      altitude: 678,
      status: 'published',
      activityCodes: ['gardening'],
      proposer: { firstName: 'Sophie', lastName: 'Martin' },
      createdAt: new Date('2026-11-01T08:00:00Z'),
      archivedAt: undefined,
    });
  });

  it('keeps nothing of the JSON-LD envelope', () => {
    const model = mapToModel(apiProposal());
    expect(model).not.toHaveProperty('@id');
    expect(model).not.toHaveProperty('@type');
    expect(model).not.toHaveProperty('place');
  });

  it('turns the API nulls into undefined, and a missing text into an empty one', () => {
    const model = mapToSummaryModel(
      apiProposal({ title: null, city: null, altitude: null, place: null, activities: undefined }),
    );
    expect(model).toMatchObject({ title: '', activityCodes: [] });
    expect(model.city).toBeUndefined();
    expect(model.altitude).toBeUndefined();
    expect(model.status).toBeUndefined();
  });

  it('gives no status for a workflow place the front does not know', () => {
    expect(mapToModel(apiProposal({ place: 'added_by_the_api_since' })).status).toBeUndefined();
  });

  it('rejects a proposal without a uuid', () => {
    expect(() => mapToModel(apiProposal({ uuid: null }))).toThrow('area proposal uuid');
  });

  it('builds the creation body under the API names', () => {
    expect(
      mapToCreateBody({
        title: 'Terrain',
        description: 'Ensoleillé',
        surfaceTotal: 300,
        surfaceToShare: 130,
        city: 'Lille',
        altitude: 678,
        activityCodes: ['gardening'],
      }),
    ).toEqual({
      title: 'Terrain',
      description: 'Ensoleillé',
      surfaceTotal: 300,
      surfaceToShare: 130,
      city: 'Lille',
      altitude: 678,
      activities: ['gardening'],
    });
  });

  it('only puts the fields set in the merge patch', () => {
    expect(mapToUpdateBody({ title: 'Nouveau titre', activityCodes: [] })).toEqual({
      title: 'Nouveau titre',
      activities: [],
    });
  });
});
