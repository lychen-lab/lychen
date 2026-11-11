import type {
  ApiLandsUlidGetData,
  ApiLandsUlidGetResponse,
} from '@lychen/typescript-tera-api-sdk/generated/tera-api';
import { optional, required, toDate } from '@lychen/typescript-api-mapping/mapping';
import type { Land } from '@lychen/typescript-tera-models/Land';
import type { ApiQueryOptions } from '@lychen/vue-api-query/options';
import { useApiQuery } from '@lychen/vue-api-query/useApiQuery';
import type { MaybeRefOrGetter } from 'vue';
import { useTeraApi } from '../../client/useTeraApi';
import { landKeys } from './queryKeys';

/** The land's ulid. */
export type GetLandParams = string;

/** The cache key of `useGetLand`. */
export function getLandQueryKey(params: GetLandParams | undefined) {
  return landKeys.of('get-land', params);
}

/**
 * One land, `GET /api/lands/{ulid}`. The query waits while the
 * params are undefined, and fetches again whenever they change.
 */
export function useGetLand(
  params: MaybeRefOrGetter<GetLandParams | undefined>,
  options: ApiQueryOptions<Land> = {},
) {
  const { api } = useTeraApi();

  return useApiQuery(
    params,
    {
      queryKey: getLandQueryKey,
      async fetch(current) {
        const { data } = await api.apiLandsUlidGet({
          ...mapToRequest(current),
          throwOnError: true,
        });
        return mapToModel(data);
      },
    },
    options,
  );
}

/** The request these params make, under the API's names. */
export function mapToRequest(params: GetLandParams): Omit<ApiLandsUlidGetData, 'url'> {
  return {
    path: { ulid: params },
  };
}

/**
 * The response as the front's model: list its fields one by one, under the front's
 * names, never with a spread.
 */
export function mapToModel(data: ApiLandsUlidGetResponse): Land {
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
