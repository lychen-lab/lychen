import type { ApiMutationOptions } from '@lychen/vue-api-query/options';
import { useMutation, useQueryClient } from '@tanstack/vue-query';
import { useTeraApi } from '../../../client/useTeraApi';
import { landKeys } from '../queryKeys';

/**
 * Deletes a land, `DELETE /api/lands/{ulid}`, given its ulid. By default, it then drops it
 * from the cache and refetches the lists.
 */
export function useDeleteLand(options: ApiMutationOptions<void, string> = {}) {
  const { api } = useTeraApi();
  const queryClient = useQueryClient();

  return useMutation({
    onSuccess: (_, ulid) => {
      // Nothing left to fetch: a refetch would only fail.
      queryClient.removeQueries({ queryKey: landKeys.detail(ulid) });
      return queryClient.invalidateQueries({ queryKey: landKeys.lists() });
    },
    ...options,
    async mutationFn(ulid: string) {
      await api.apiLandsUlidDelete({ path: { ulid }, throwOnError: true });
    },
  });
}
