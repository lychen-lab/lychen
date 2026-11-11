import type {
  ApiAreaRequestsPostData,
  ApiAreaRequestsPostResponse,
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
  type AreaRequestCreateInput,
} from '@lychen/typescript-espace-models/AreaRequest';
import type { ApiMutationOptions } from '@lychen/vue-api-query/options';
import { useApiMutation } from '@lychen/vue-api-query/useApiMutation';
import { useEspaceApi } from '../../client/useEspaceApi';
import { toPersonName } from '../person';
import { areaRequestKeys } from './queryKeys';

/** Creates an area request, `POST /api/area_requests`, and resolves with it. */
export function useCreateAreaRequest(
  options: ApiMutationOptions<AreaRequest, AreaRequestCreateInput> = {},
) {
  const { api } = useEspaceApi();

  return useApiMutation(
    {
      async send(input: AreaRequestCreateInput) {
        const { data } = await api.apiAreaRequestsPost({
          ...mapToRequest(input),
          throwOnError: true,
        });
        return mapToModel(data);
      },
      invalidates: () => [areaRequestKeys.all],
    },
    options,
  );
}

/** The request, from the front's input. */
export function mapToRequest(input: AreaRequestCreateInput): Omit<ApiAreaRequestsPostData, 'url'> {
  return {
    body: withoutUndefined({
      uuid: input.uuid,
      title: input.title,
      description: input.description,
      minimalSurfaceRequested: input.minimalSurfaceRequested,
      city: input.city,
      activities: input.activityCodes ?? [],
    }),
  };
}

/** The created request as the front's model. */
export function mapToModel(data: ApiAreaRequestsPostResponse): AreaRequest {
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
