import type { Land } from '@lychen/typescript-tera-models/Land';
import type { ApiQueryOptions } from '@lychen/vue-api-query/options';
import { useItemQuery } from '@lychen/vue-api-query/useItemQuery';
import type { MaybeRefOrGetter } from 'vue';
import { useTeraApi } from '../../client/useTeraApi';
import { mapToModel } from './mapper';
import { landKeys } from './queryKeys';

/**
 * The land of this ulid, `GET /api/lands/{ulid}`. The query waits for the
 * ulid, and fetches again whenever it changes.
 */
export function useGetLand(
  ulid: MaybeRefOrGetter<string | undefined>,
  options: ApiQueryOptions<Land> = {},
) {
  const { api } = useTeraApi();

  return useItemQuery(
    ulid,
    {
      queryKey: landKeys.detail,
      async fetch(id) {
        const { data } = await api.apiLandsUlidGet({ path: { ulid: id }, throwOnError: true });
        return mapToModel(data);
      },
    },
    options,
  );
}
