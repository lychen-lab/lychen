import type { ApiMutationOptions } from '@lychen/vue-api-query/options';
import { useDeleteMutation } from '@lychen/vue-api-query/useDeleteMutation';
import { useTeraApi } from '../../client/useTeraApi';
import { landKeys } from './queryKeys';

/** `DELETE /api/lands/{ulid}`, given the land's ulid. */
export function useDeleteLand(options: ApiMutationOptions<void, string> = {}) {
  const { api } = useTeraApi();

  return useDeleteMutation(
    {
      queryKeys: landKeys,
      remove: (id) => api.apiLandsUlidDelete({ path: { ulid: id }, throwOnError: true }),
    },
    options,
  );
}
