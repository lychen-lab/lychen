import type { ApiLandProposalsUlidpublishPatchResponse } from '@lychen/typescript-tera-api-sdk/generated/tera-api';
import { oneOf, required } from '@lychen/typescript-api-mapping/mapping';
import {
  LAND_PROPOSAL_STATES,
  type LandProposal,
} from '@lychen/typescript-front-model-business/land/LandProposal';
import type { WithRequired } from '@lychen/typescript-front-model-business/WithRequired';

/** The fields of the land proposal this endpoint always gives. */
export type Model = WithRequired<LandProposal, 'id'>;

/**
 * Where the published proposal stands: the API answers with the whole proposal, the front
 * only needs which one and its state.
 */
export function mapToModel(data: ApiLandProposalsUlidpublishPatchResponse): Model {
  return {
    id: required(data.ulid, 'land proposal ulid'),
    state: oneOf(LAND_PROPOSAL_STATES, data.state),
  };
}
