import type {
  ApiSeedLibrariesGetCollectionData,
  ApiSeedLibrariesGetCollectionResponse,
} from '@lychen/typescript-flora-api-sdk/generated/flora-api';
import type { Collection } from '@lychen/typescript-api-mapping/Collection';
import {
  optional,
  required,
  toCollection,
  toDate,
  withoutUndefined,
} from '@lychen/typescript-api-mapping/mapping';
import type {
  SeedLibraryFilters,
  SeedLibrarySummary,
} from '@lychen/typescript-flora-models/SeedLibrary';
import type { ApiQueryOptions } from '@lychen/vue-api-query/options';
import { useApiQuery } from '@lychen/vue-api-query/useApiQuery';
import type { MaybeRefOrGetter } from 'vue';
import { useFloraApi } from '../../client/useFloraApi';
import { seedLibraryKeys } from './queryKeys';

/** The page and its size. */
export type GetSeedLibrariesParams = SeedLibraryFilters;

/** The cache key of `useGetSeedLibraries`. */
export function getSeedLibrariesQueryKey(params: GetSeedLibrariesParams | undefined) {
  return seedLibraryKeys.of('get-seed-libraries', params);
}

/**
 * A page of the seed libraries, `GET /api/seed_libraries`. The query waits while the
 * params are undefined, and fetches again whenever they change.
 */
export function useGetSeedLibraries(
  params: MaybeRefOrGetter<GetSeedLibrariesParams | undefined>,
  options: ApiQueryOptions<Collection<SeedLibrarySummary>> = {},
) {
  const { api } = useFloraApi();

  return useApiQuery(
    params,
    {
      queryKey: getSeedLibrariesQueryKey,
      async fetch(current) {
        const { data } = await api.apiSeedLibrariesGetCollection({
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
export function mapToRequest(
  params: GetSeedLibrariesParams,
): Omit<ApiSeedLibrariesGetCollectionData, 'url'> {
  return {
    query: withoutUndefined({ page: params.page, itemsPerPage: params.itemsPerPage }),
  };
}

/**
 * A member of the page as the front's model: list its fields one by one, under the front's
 * names, never with a spread.
 */
export function mapToModel(
  data: ApiSeedLibrariesGetCollectionResponse['member'][number],
): SeedLibrarySummary {
  return {
    uuid: required(data.uuid, 'seed library uuid'),
    name: data.name ?? '',
    city: optional(data.city),
    seedLotUuids: data.seedLots ?? [],
    createdAt: toDate(data.createdAt),
    updatedAt: toDate(data.updatedAt),
  };
}
