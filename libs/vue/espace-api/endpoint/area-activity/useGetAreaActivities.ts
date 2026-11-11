import type { ApiAreaActivitiesGetCollectionResponse } from '@lychen/typescript-espace-api-sdk/generated/espace-api';
import type { Collection } from '@lychen/typescript-api-mapping/Collection';
import {
  optional,
  required,
  toCollection,
  withoutUndefined,
} from '@lychen/typescript-api-mapping/mapping';
import type {
  AreaActivity,
  AreaActivityFilters,
} from '@lychen/typescript-espace-models/AreaActivity';
import type { ApiQueryOptions } from '@lychen/vue-api-query/options';
import { useApiQuery } from '@lychen/vue-api-query/useApiQuery';
import type { MaybeRefOrGetter } from 'vue';
import { useEspaceApi } from '../../client/useEspaceApi';
import { areaActivityKeys } from './queryKeys';

/**
 * A page of the area activities, `GET /api/area_activities`. The query fetches again
 * whenever the filters change.
 */
export function useGetAreaActivities(
  filters: MaybeRefOrGetter<AreaActivityFilters> = {},
  options: ApiQueryOptions<Collection<AreaActivity>> = {},
) {
  const { api } = useEspaceApi();

  return useApiQuery(
    filters,
    {
      queryKey: (params) => areaActivityKeys.of('get-area-activities', params),
      async fetch(params) {
        const { data } = await api.apiAreaActivitiesGetCollection({
          ...mapToRequest(params),
          throwOnError: true,
        });
        return toCollection(data, mapToModel);
      },
    },
    options,
  );
}

/** The filters under the API's names. */
export function mapToRequest({ page, itemsPerPage }: AreaActivityFilters) {
  return { query: withoutUndefined({ page, itemsPerPage }) };
}

/** An activity of the page as the front's model. */
export function mapToModel(
  data: ApiAreaActivitiesGetCollectionResponse['member'][number],
): AreaActivity {
  return {
    uuid: required(data.uuid, 'area activity uuid'),
    code: optional(data.code),
  };
}
