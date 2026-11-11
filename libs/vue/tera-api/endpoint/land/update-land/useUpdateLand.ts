import type { ApiMutationOptions } from '@lychen/vue-api-query/options';
import { useMutation, useQueryClient } from '@tanstack/vue-query';
import { useTeraApi } from '../../../client/useTeraApi';
import { landKeys } from '../queryKeys';
import { mapToModel, type Model } from './mapToModel';
import { mapToRequest, type UpdateLandVariables } from './mapToRequest';

/**
 * Changes a land, `PATCH /api/lands/{ulid}`, and resolves with it. By default, it then
 * refetches that land and the lists.
 */
export function useUpdateLand(options: ApiMutationOptions<Model, UpdateLandVariables> = {}) {
  const { api } = useTeraApi();
  const queryClient = useQueryClient();

  return useMutation({
    onSuccess: (_, { id }) =>
      Promise.all([
        queryClient.invalidateQueries({ queryKey: landKeys.detail(id) }),
        queryClient.invalidateQueries({ queryKey: landKeys.lists() }),
      ]),
    ...options,
    async mutationFn(variables: UpdateLandVariables) {
      const { data } = await api.apiLandsUlidPatch({
        ...mapToRequest(variables),
        throwOnError: true,
      });
      return mapToModel(data);
    },
  });
}
