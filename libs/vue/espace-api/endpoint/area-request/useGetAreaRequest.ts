import type { ApiAreaRequestsUuidGetResponse } from '@lychen/typescript-espace-api-sdk/generated/espace-api';
import { oneOf, optional, required, toDate } from '@lychen/typescript-api-mapping/mapping';
import {
  AREA_REQUEST_STATUSES,
  type AreaRequest,
} from '@lychen/typescript-espace-models/AreaRequest';
import type { ApiQueryOptions } from '@lychen/vue-api-query/options';
import { useApiQuery } from '@lychen/vue-api-query/useApiQuery';
import type { MaybeRefOrGetter } from 'vue';
import { useEspaceApi } from '../../client/useEspaceApi';
import { toPersonName } from '../person';
import { areaRequestKeys } from './queryKeys';

/** The cache key of `useGetAreaRequest`. */
export function getAreaRequestQueryKey(uuid: string | undefined) {
  return areaRequestKeys.of('get-area-request', uuid);
}

/**
 * One area request, `GET /api/area_requests/{uuid}`. The query waits for a uuid, and fetches again
 * whenever it changes.
 */
export function useGetAreaRequest(
  uuid: MaybeRefOrGetter<string | undefined>,
  options: ApiQueryOptions<AreaRequest> = {},
) {
  const { api } = useEspaceApi();

  return useApiQuery(
    uuid,
    {
      queryKey: getAreaRequestQueryKey,
      async fetch(id) {
        const { data } = await api.apiAreaRequestsUuidGet({
          path: { uuid: id },
          throwOnError: true,
        });
        return mapToModel(data);
      },
    },
    options,
  );
}

/** What this endpoint returns, as the front's model. */
export function mapToModel(data: ApiAreaRequestsUuidGetResponse): AreaRequest {
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
