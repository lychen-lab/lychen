import type { SeedLibrary } from '@lychen/typescript-flora-models/SeedLibrary';
import type { ApiQueryOptions } from '@lychen/vue-api-query/options';
import { useItemQuery } from '@lychen/vue-api-query/useItemQuery';
import type { MaybeRefOrGetter } from 'vue';
import { useFloraApi } from '../../client/useFloraApi';
import { mapToModel } from './mapper';
import { seedLibraryKeys } from './queryKeys';

/**
 * The seed library of this uuid, `GET /api/seed_libraries/{uuid}`. The query waits for the
 * uuid, and fetches again whenever it changes.
 */
export function useGetSeedLibrary(
  uuid: MaybeRefOrGetter<string | undefined>,
  options: ApiQueryOptions<SeedLibrary> = {},
) {
  const { api } = useFloraApi();

  return useItemQuery(
    uuid,
    {
      queryKey: seedLibraryKeys.detail,
      async fetch(id) {
        const { data } = await api.apiSeedLibrariesUuidGet({
          path: { uuid: id },
          throwOnError: true,
        });
        return mapToModel(data);
      },
    },
    options,
  );
}
