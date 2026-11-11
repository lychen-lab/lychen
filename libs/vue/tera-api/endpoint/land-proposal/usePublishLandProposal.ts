import type {
  ApiLandProposalsUlidpublishPatchData,
  ApiLandProposalsUlidpublishPatchResponse,
} from '@lychen/typescript-tera-api-sdk/generated/tera-api';
import { oneOf, required } from '@lychen/typescript-api-mapping/mapping';
import {
  LAND_PROPOSAL_STATES,
  type LandProposalStateChange,
} from '@lychen/typescript-tera-models/LandProposalStateChange';
import type { ApiMutationOptions } from '@lychen/vue-api-query/options';
import { useApiMutation } from '@lychen/vue-api-query/useApiMutation';
import { useTeraApi } from '../../client/useTeraApi';
import { landProposalKeys } from './queryKeys';

export interface PublishLandProposalVariables {
  ulid: string;
}

/** Publishes a land proposal, the `publish` transition of its workflow
 * (`PATCH /api/land_proposals/{ulid}/publish`), and resolves with where it stands now. */
export function usePublishLandProposal(
  options: ApiMutationOptions<LandProposalStateChange, PublishLandProposalVariables> = {},
) {
  const { api } = useTeraApi();

  return useApiMutation(
    {
      async send(variables: PublishLandProposalVariables) {
        const { data } = await api.apiLandProposalsUlidpublishPatch({
          ...mapToRequest(variables),
          throwOnError: true,
        });
        return mapToModel(data);
      },
      invalidates: () => [landProposalKeys.all],
    },
    options,
  );
}

/** The request, from the front's input, under the API's names. */
export function mapToRequest(
  variables: PublishLandProposalVariables,
): Omit<ApiLandProposalsUlidpublishPatchData, 'url'> {
  return {
    path: { ulid: variables.ulid },
    // A transition takes no field.
    body: {},
  };
}

/**
 * The response as the front's model: list its fields one by one, under the front's names,
 * never with a spread.
 */
export function mapToModel(
  data: ApiLandProposalsUlidpublishPatchResponse,
): LandProposalStateChange {
  return {
    ulid: required(data.ulid, 'land proposal ulid'),
    state: oneOf(LAND_PROPOSAL_STATES, data.state),
  };
}
