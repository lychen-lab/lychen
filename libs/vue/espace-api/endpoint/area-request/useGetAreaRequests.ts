import type { ApiAreaRequestsGetCollectionResponse } from '@lychen/typescript-espace-api-sdk/generated/espace-api';
import type { Collection } from '@lychen/typescript-api-mapping/Collection';
import {
  oneOf,
  optional,
  required,
  toCollection,
  withoutUndefined,
} from '@lychen/typescript-api-mapping/mapping';
import {
  AREA_REQUEST_STATUSES,
  type AreaRequestFilters,
  type AreaRequestSummary,
} from '@lychen/typescript-espace-models/AreaRequest';
import type { ApiQueryOptions } from '@lychen/vue-api-query/options';
import { useApiQuery } from '@lychen/vue-api-query/useApiQuery';
import type { MaybeRefOrGetter } from 'vue';
import { useEspaceApi } from '../../client/useEspaceApi';
import { toPersonName } from '../person';
import { areaRequestKeys } from './queryKeys';

/**
 * A page of the area requests the user may see, `GET /api/area_requests`. The query
 * fetches again whenever the filters change.
 */
export function useGetAreaRequests(
  filters: MaybeRefOrGetter<AreaRequestFilters> = {},
  options: ApiQueryOptions<Collection<AreaRequestSummary>> = {},
) {
  const { api } = useEspaceApi();

  return useApiQuery(
    filters,
    {
      queryKey: (params) => areaRequestKeys.of('get-area-requests', params),
      async fetch(params) {
        const { data } = await api.apiAreaRequestsGetCollection({
          ...mapToRequest(params),
          throwOnError: true,
        });
        return toCollection(data, mapToModel);
      },
    },
    options,
  );
}

/** The filters under the API's names: the front's status is the API's workflow place. */
export function mapToRequest({ status, page, itemsPerPage }: AreaRequestFilters) {
  return { query: withoutUndefined({ place: status, page, itemsPerPage }) };
}

/** A request of the page as the front's model. */
export function mapToModel(
  data: ApiAreaRequestsGetCollectionResponse['member'][number],
): AreaRequestSummary {
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
  };
}
