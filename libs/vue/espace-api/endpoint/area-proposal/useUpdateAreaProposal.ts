import type {
  ApiAreaProposalsUuidPatchData,
  ApiAreaProposalsUuidPatchResponse,
} from '@lychen/typescript-espace-api-sdk/generated/espace-api';
import {
  oneOf,
  optional,
  required,
  toDate,
  withoutUndefined,
} from '@lychen/typescript-api-mapping/mapping';
import {
  AREA_PROPOSAL_STATUSES,
  type AreaProposal,
  type AreaProposalUpdateInput,
} from '@lychen/typescript-espace-models/AreaProposal';
import type { ApiMutationOptions } from '@lychen/vue-api-query/options';
import { useApiMutation } from '@lychen/vue-api-query/useApiMutation';
import { useEspaceApi } from '../../client/useEspaceApi';
import { toPersonName } from '../person';
import { areaProposalKeys } from './queryKeys';

export interface UpdateAreaProposalVariables {
  uuid: string;
  /** The fields to change; those left out are kept. */
  input: AreaProposalUpdateInput;
}

/** Changes an area proposal, `PATCH /api/area_proposals/{uuid}`, and resolves with it. */
export function useUpdateAreaProposal(
  options: ApiMutationOptions<AreaProposal, UpdateAreaProposalVariables> = {},
) {
  const { api } = useEspaceApi();

  return useApiMutation(
    {
      async send(variables: UpdateAreaProposalVariables) {
        const { data } = await api.apiAreaProposalsUuidPatch({
          ...mapToRequest(variables),
          throwOnError: true,
        });
        return mapToModel(data);
      },
      invalidates: () => [areaProposalKeys.all],
    },
    options,
  );
}

/** The request, from the front's input: a JSON Merge Patch of the fields set only. */
export function mapToRequest({
  uuid,
  input,
}: UpdateAreaProposalVariables): Omit<ApiAreaProposalsUuidPatchData, 'url'> {
  return {
    path: { uuid },
    body: withoutUndefined({
      title: input.title,
      description: input.description,
      surfaceTotal: input.surfaceTotal,
      surfaceToShare: input.surfaceToShare,
      city: input.city,
      altitude: input.altitude,
      activities: input.activityCodes,
    }),
  };
}

/** The changed proposal as the front's model. */
export function mapToModel(data: ApiAreaProposalsUuidPatchResponse): AreaProposal {
  return {
    uuid: required(data.uuid, 'area proposal uuid'),
    title: data.title ?? '',
    description: data.description ?? '',
    surfaceTotal: optional(data.surfaceTotal),
    surfaceToShare: optional(data.surfaceToShare),
    city: optional(data.city),
    altitude: optional(data.altitude),
    // The API exposes its workflow's marking: the front only knows a status.
    status: oneOf(AREA_PROPOSAL_STATUSES, data.place),
    activityCodes: data.activities ?? [],
    proposer: toPersonName(data.proposerFirstName, data.proposerLastName),
    createdAt: toDate(data.createdAt),
    archivedAt: toDate(data.archivedAt),
  };
}
