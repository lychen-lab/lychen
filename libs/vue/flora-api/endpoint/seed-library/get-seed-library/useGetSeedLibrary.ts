import type { ApiQueryOptions } from '@lychen/vue-api-query/options';
import { useQuery, type QueryKey } from '@tanstack/vue-query';
import { computed, toValue, type MaybeRefOrGetter } from 'vue';
import { useFloraApi } from '../../../client/useFloraApi';
import { seedLibraryKeys } from '../queryKeys';
import { mapToModel, type Model } from './mapToModel';

/**
 * One seed library, `GET /api/seed_libraries/{uuid}`. The query waits for an id, and
 * fetches again whenever it changes.
 */
export function useGetSeedLibrary(
  id: MaybeRefOrGetter<string | undefined>,
  options: ApiQueryOptions<Model> = {},
) {
  const { api } = useFloraApi();
  const { enabled = true, ...queryOptions } = options;

  return useQuery<Model, Error, Model, QueryKey>({
    queryKey: computed(() => seedLibraryKeys.detail(toValue(id) ?? '')),
    ...queryOptions,
    enabled: computed(() => Boolean(toValue(id)) && toValue(enabled)),
    async queryFn() {
      const current = toValue(id);
      if (!current) {
        throw new Error('useGetSeedLibrary: there is no id to fetch yet.');
      }
      const { data } = await api.apiSeedLibrariesUuidGet({
        path: { uuid: current },
        throwOnError: true,
      });
      return mapToModel(data);
    },
  });
}
