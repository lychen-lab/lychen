import type { ApiLandProposalsUlidpublishPatchResponse } from '@lychen/typescript-tera-api-sdk/generated/tera-api';
import { describe, expect, it, vi } from 'vitest';
import { mapToModel, mapToRequest } from './usePublishLandProposal';

vi.mock('../../client/useTeraApi', () => ({ useTeraApi: () => ({ api: {} }) }));

const ULID = '01JD0R3C5E7G9J1L3N5Q7S9V1X';

describe('the publish transition of a land proposal', () => {
  it('sends no field, to the transition of the proposal', () => {
    expect(mapToRequest({ ulid: ULID })).toEqual({ path: { ulid: ULID }, body: {} });
  });

  it('gives where the proposal stands, not the whole proposal the API answers with', () => {
    const response: ApiLandProposalsUlidpublishPatchResponse = {
      '@id': `/api/land_proposals/${ULID}`,
      '@type': 'LandProposal',
      ulid: ULID,
      title: 'Jardin à partager',
      gardenTotalSurface: 120,
      hasWaterPoint: true,
      state: 'published',
    };
    expect(mapToModel(response)).toStrictEqual({ ulid: ULID, state: 'published' });
  });
});
