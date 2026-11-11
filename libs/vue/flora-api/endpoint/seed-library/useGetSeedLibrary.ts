import type {
  ApiSeedLibrariesUuidGetData,
  ApiSeedLibrariesUuidGetResponse,
} from '@lychen/typescript-flora-api-sdk/generated/flora-api';
import { optional, required, toDate } from '@lychen/typescript-api-mapping/mapping';
import type { SeedLibrary } from '@lychen/typescript-flora-models/SeedLibrary';
import type { ApiQueryOptions } from '@lychen/vue-api-query/options';
import { useApiQuery } from '@lychen/vue-api-query/useApiQuery';
import type { MaybeRefOrGetter } from 'vue';
import { useFloraApi } from '../../client/useFloraApi';
import { seedLibraryKeys } from './queryKeys';

/** The library's uuid. */
export type GetSeedLibraryParams = string;

/** The cache key of `useGetSeedLibrary`. */
export function getSeedLibraryQueryKey(params: GetSeedLibraryParams | undefined) {
  return seedLibraryKeys.of('get-seed-library', params);
}

/**
 * One seed library, `GET /api/seed_libraries/{uuid}`. The query waits while the
 * params are undefined, and fetches again whenever they change.
 */
export function useGetSeedLibrary(
  params: MaybeRefOrGetter<GetSeedLibraryParams | undefined>,
  options: ApiQueryOptions<SeedLibrary> = {},
) {
  const { api } = useFloraApi();

  return useApiQuery(
    params,
    {
      queryKey: getSeedLibraryQueryKey,
      async fetch(current) {
        const { data } = await api.apiSeedLibrariesUuidGet({
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
export function mapToRequest(
  params: GetSeedLibraryParams,
): Omit<ApiSeedLibrariesUuidGetData, 'url'> {
  return {
    path: { uuid: params },
  };
}

/**
 * The response as the front's model: list its fields one by one, under the front's
 * names, never with a spread.
 */
export function mapToModel(data: ApiSeedLibrariesUuidGetResponse): SeedLibrary {
  return {
    uuid: required(data.uuid, 'seed library uuid'),
    name: data.name ?? '',
    city: optional(data.city),
    seedLotUuids: data.seedLots ?? [],
    createdAt: toDate(data.createdAt),
    updatedAt: toDate(data.updatedAt),
  };
}
