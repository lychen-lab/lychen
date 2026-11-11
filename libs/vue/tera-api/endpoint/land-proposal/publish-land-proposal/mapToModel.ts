import type { ApiLandProposalsUlidpublishPatchResponse } from '@lychen/typescript-tera-api-sdk/generated/tera-api';
import { oneOf, required } from '@lychen/typescript-api-mapping/mapping';
import {
  LAND_PROPOSAL_STATES,
  type LandProposalStateChange,
} from '@lychen/typescript-tera-models/LandProposalStateChange';

/**
 * Where the published proposal stands: the API answers with the whole proposal, the front
 * only needs which one and its state.
 */
export function mapToModel(
  data: ApiLandProposalsUlidpublishPatchResponse,
): LandProposalStateChange {
  return {
    ulid: required(data.ulid, 'land proposal ulid'),
    state: oneOf(LAND_PROPOSAL_STATES, data.state),
  };
}
