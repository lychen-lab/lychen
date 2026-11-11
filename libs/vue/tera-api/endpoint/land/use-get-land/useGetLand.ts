import type { Land } from '@lychen/typescript-tera-models/Land';
import type { ApiQueryOptions } from '@lychen/vue-api-query/options';
import { useQuery, type QueryKey } from '@tanstack/vue-query';
import { computed, toValue, type MaybeRefOrGetter } from 'vue';
import { useTeraApi } from '../../../client/useTeraApi';
import { landKeys } from '../queryKeys';
import { mapToModel } from './mapToModel';

/**
 * One land, `GET /api/lands/{ulid}`. The query waits for a ulid, and fetches again
 * whenever it changes.
 */
export function useGetLand(
  ulid: MaybeRefOrGetter<string | undefined>,
  options: ApiQueryOptions<Land> = {},
) {
  const { api } = useTeraApi();
  const { enabled = true, ...queryOptions } = options;

  return useQuery<Land, Error, Land, QueryKey>({
    queryKey: computed(() => landKeys.detail(toValue(ulid) ?? '')),
    ...queryOptions,
    enabled: computed(() => Boolean(toValue(ulid)) && toValue(enabled)),
    async queryFn() {
      const current = toValue(ulid);
      if (!current) {
        throw new Error('useGetLand: there is no ulid to fetch yet.');
      }
      const { data } = await api.apiLandsUlidGet({ path: { ulid: current }, throwOnError: true });
      return mapToModel(data);
    },
  });
}
