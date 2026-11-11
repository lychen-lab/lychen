import type { ApiMutationOptions } from '@lychen/vue-api-query/options';
import { useMutation, useQueryClient } from '@tanstack/vue-query';
import { useFloraApi } from '../../../client/useFloraApi';
import { seedLibraryKeys } from '../queryKeys';

/**
 * Deletes a seed library, `DELETE /api/seed_libraries/{uuid}`, given its id. By default,
 * it then drops it from the cache and refetches the lists.
 */
export function useDeleteSeedLibrary(options: ApiMutationOptions<void, string> = {}) {
  const { api } = useFloraApi();
  const queryClient = useQueryClient();

  return useMutation({
    onSuccess: (_, id) => {
      // Nothing left to fetch: a refetch would only fail.
      queryClient.removeQueries({ queryKey: seedLibraryKeys.detail(id) });
      return queryClient.invalidateQueries({ queryKey: seedLibraryKeys.lists() });
    },
    ...options,
    async mutationFn(id: string) {
      await api.apiSeedLibrariesUuidDelete({ path: { uuid: id }, throwOnError: true });
    },
  });
}
