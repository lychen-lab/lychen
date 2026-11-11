import type {
  ApiSeedLotsUuidGetData,
  ApiSeedLotsUuidGetResponse,
} from '@lychen/typescript-flora-api-sdk/generated/flora-api';
import { oneOf, optional, required, toDate } from '@lychen/typescript-api-mapping/mapping';
import { SEED_LOT_STATUSES, type SeedLot } from '@lychen/typescript-flora-models/SeedLot';
import type { ApiQueryOptions } from '@lychen/vue-api-query/options';
import { useApiQuery } from '@lychen/vue-api-query/useApiQuery';
import type { MaybeRefOrGetter } from 'vue';
import { useFloraApi } from '../../client/useFloraApi';
import { seedLotKeys } from './queryKeys';

/** The lot's uuid. */
export type GetSeedLotParams = string;

/** The cache key of `useGetSeedLot`. */
export function getSeedLotQueryKey(params: GetSeedLotParams | undefined) {
  return seedLotKeys.of('get-seed-lot', params);
}

/**
 * One seed lot, `GET /api/seed_lots/{uuid}`. The query waits while the
 * params are undefined, and fetches again whenever they change.
 */
export function useGetSeedLot(
  params: MaybeRefOrGetter<GetSeedLotParams | undefined>,
  options: ApiQueryOptions<SeedLot> = {},
) {
  const { api } = useFloraApi();

  return useApiQuery(
    params,
    {
      queryKey: getSeedLotQueryKey,
      async fetch(current) {
        const { data } = await api.apiSeedLotsUuidGet({
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
export function mapToRequest(params: GetSeedLotParams): Omit<ApiSeedLotsUuidGetData, 'url'> {
  return {
    path: { uuid: params },
  };
}

/**
 * The response as the front's model: list its fields one by one, under the front's
 * names, never with a spread.
 */
export function mapToModel(data: ApiSeedLotsUuidGetResponse): SeedLot {
  return {
    uuid: required(data.uuid, 'seed lot uuid'),
    variety: data.variety ?? '',
    harvestYear: optional(data.harvestYear),
    quantityInGrams: optional(data.quantityInGrams),
    seedLibraryUuid: optional(data.seedLibrary),
    characteristicUuids: data.characteristics ?? [],
    // The API exposes its workflow's marking: the front only knows a status.
    status: oneOf(SEED_LOT_STATUSES, data.state),
    createdAt: toDate(data.createdAt),
    updatedAt: toDate(data.updatedAt),
  };
}
