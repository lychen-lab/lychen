import type {
  ApiAreaRequestsUuidPatchData,
  ApiAreaRequestsUuidPatchResponse,
} from '@lychen/typescript-espace-api-sdk/generated/espace-api';
import {
  oneOf,
  optional,
  required,
  toDate,
  withoutUndefined,
} from '@lychen/typescript-api-mapping/mapping';
import {
  AREA_REQUEST_STATUSES,
  type AreaRequest,
  type AreaRequestUpdateInput,
} from '@lychen/typescript-espace-models/AreaRequest';
import type { ApiMutationOptions } from '@lychen/vue-api-query/options';
import { useApiMutation } from '@lychen/vue-api-query/useApiMutation';
import { useEspaceApi } from '../../client/useEspaceApi';
import { toPersonName } from '../person';
import { areaRequestKeys } from './queryKeys';

export interface UpdateAreaRequestVariables {
  uuid: string;
  /** The fields to change; those left out are kept. */
  input: AreaRequestUpdateInput;
}

/** Changes an area request, `PATCH /api/area_requests/{uuid}`, and resolves with it. */
export function useUpdateAreaRequest(
  options: ApiMutationOptions<AreaRequest, UpdateAreaRequestVariables> = {},
) {
  const { api } = useEspaceApi();

  return useApiMutation(
    {
      async send(variables: UpdateAreaRequestVariables) {
        const { data } = await api.apiAreaRequestsUuidPatch({
          ...mapToRequest(variables),
          throwOnError: true,
        });
        return mapToModel(data);
      },
      invalidates: () => [areaRequestKeys.all],
    },
    options,
  );
}

/** The request, from the front's input: a JSON Merge Patch of the fields set only. */
export function mapToRequest({
  uuid,
  input,
}: UpdateAreaRequestVariables): Omit<ApiAreaRequestsUuidPatchData, 'url'> {
  return {
    path: { uuid },
    body: withoutUndefined({
      title: input.title,
      description: input.description,
      minimalSurfaceRequested: input.minimalSurfaceRequested,
      city: input.city,
      activities: input.activityCodes,
    }),
  };
}

/** The changed request as the front's model. */
export function mapToModel(data: ApiAreaRequestsUuidPatchResponse): AreaRequest {
  return {
    uuid: required(data.uuid, 'area request uuid'),
    title: data.title ?? '',
    description: data.description ?? '',
    minimalSurfaceRequested: optional(data.minimalSurfaceRequested),
    city: optional(data.city),
    // The API exposes its workflow's marking: the front only knows a status.
    status: oneOf(AREA_REQUEST_STATUSES, data.place),
    activityCodes: data.activities ?? [],
    requester: toPersonName(data.requesterFirstName, data.requesterLastName),
    createdAt: toDate(data.createdAt),
    archivedAt: toDate(data.archivedAt),
  };
}
