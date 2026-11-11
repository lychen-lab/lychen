import type { ApiMutationOptions } from '@lychen/vue-api-query/options';
import { useDeleteMutation } from '@lychen/vue-api-query/useDeleteMutation';
import { useFloraApi } from '../../client/useFloraApi';
import { seedLibraryKeys } from './queryKeys';

/** `DELETE /api/seed_libraries/{uuid}`, given the seed library's uuid. */
export function useDeleteSeedLibrary(options: ApiMutationOptions<void, string> = {}) {
  const { api } = useFloraApi();

  return useDeleteMutation(
    {
      queryKeys: seedLibraryKeys,
      remove: (id) => api.apiSeedLibrariesUuidDelete({ path: { uuid: id }, throwOnError: true }),
    },
    options,
  );
}
