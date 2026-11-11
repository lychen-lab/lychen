import type {
  ApiAreaProposalsPostData,
  ApiAreaProposalsPostResponse,
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
  type AreaProposalCreateInput,
} from '@lychen/typescript-espace-models/AreaProposal';
import type { ApiMutationOptions } from '@lychen/vue-api-query/options';
import { useApiMutation } from '@lychen/vue-api-query/useApiMutation';
import { useEspaceApi } from '../../client/useEspaceApi';
import { toPersonName } from '../person';
import { areaProposalKeys } from './queryKeys';

/** Creates an area proposal, `POST /api/area_proposals`, and resolves with it. */
export function useCreateAreaProposal(
  options: ApiMutationOptions<AreaProposal, AreaProposalCreateInput> = {},
) {
  const { api } = useEspaceApi();

  return useApiMutation(
    {
      async send(input: AreaProposalCreateInput) {
        const { data } = await api.apiAreaProposalsPost({
          ...mapToRequest(input),
          throwOnError: true,
        });
        return mapToModel(data);
      },
      invalidates: () => [areaProposalKeys.all],
    },
    options,
  );
}

/** The request, from the front's input. */
export function mapToRequest(
  input: AreaProposalCreateInput,
): Omit<ApiAreaProposalsPostData, 'url'> {
  return {
    body: withoutUndefined({
      uuid: input.uuid,
      title: input.title,
      description: input.description,
      surfaceTotal: input.surfaceTotal,
      surfaceToShare: input.surfaceToShare,
      city: input.city,
      altitude: input.altitude,
      activities: input.activityCodes ?? [],
    }),
  };
}

/** The created proposal as the front's model. */
export function mapToModel(data: ApiAreaProposalsPostResponse): AreaProposal {
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
