import type {
  ApiLandsGetCollectionData,
  ApiLandsGetCollectionResponse,
} from '@lychen/typescript-tera-api-sdk/generated/tera-api';
import {
  optional,
  required,
  toCollection,
  toDate,
  withoutUndefined,
} from '@lychen/typescript-api-mapping/mapping';
import type { Collection } from '@lychen/typescript-api-mapping/Collection';
import type { LandFilters, LandSummary } from '@lychen/typescript-tera-models/Land';
import type { ApiQueryOptions } from '@lychen/vue-api-query/options';
import { useApiQuery } from '@lychen/vue-api-query/useApiQuery';
import type { MaybeRefOrGetter } from 'vue';
import { useTeraApi } from '../../client/useTeraApi';
import { landKeys } from './queryKeys';

/** The page and its size. */
export type GetLandsParams = LandFilters;

/** The cache key of `useGetLands`. */
export function getLandsQueryKey(params: GetLandsParams | undefined) {
  return landKeys.of('get-lands', params);
}

/**
 * A page of the user's lands, `GET /api/lands`. The query waits while the
 * params are undefined, and fetches again whenever they change.
 */
export function useGetLands(
  params: MaybeRefOrGetter<GetLandsParams | undefined>,
  options: ApiQueryOptions<Collection<LandSummary>> = {},
) {
  const { api } = useTeraApi();

  return useApiQuery(
    params,
    {
      queryKey: getLandsQueryKey,
      async fetch(current) {
        const { data } = await api.apiLandsGetCollection({
          ...mapToRequest(current),
          throwOnError: true,
        });
        return toCollection(data, mapToModel);
      },
    },
    options,
  );
}

/** The request these params make, under the API's names. */
export function mapToRequest(params: GetLandsParams): Omit<ApiLandsGetCollectionData, 'url'> {
  return {
    query: withoutUndefined({ page: params.page, itemsPerPage: params.itemsPerPage }),
  };
}

/**
 * A member of the page as the front's model: list its fields one by one, under the front's
 * names, never with a spread.
 */
export function mapToModel(data: ApiLandsGetCollectionResponse['member'][number]): LandSummary {
  return {
    ulid: required(data.ulid, 'land ulid'),
    name: data.name ?? '',
    surface: optional(data.surface),
    altitude: optional(data.altitude),
    createdAt: toDate(data.createdAt),
    updatedAt: toDate(data.updatedAt),
    // landMembers, landAreas and landSetting are IRIs of other resources: left out until a
    // screen needs them, then mapped to their ulids.
  };
}
