import type { ApiMutationOptions } from '@lychen/vue-api-query/options';
import { useMutation, useQueryClient } from '@tanstack/vue-query';
import { useFloraApi } from '../../../client/useFloraApi';
import { seedLibraryKeys } from '../queryKeys';
import { mapToModel, type Model } from './mapToModel';
import { mapToRequest, type UpdateSeedLibraryVariables } from './mapToRequest';

/**
 * Changes a seed library, `PATCH /api/seed_libraries/{uuid}`, and resolves with it. By
 * default, it then refetches that library and the lists.
 */
export function useUpdateSeedLibrary(
  options: ApiMutationOptions<Model, UpdateSeedLibraryVariables> = {},
) {
  const { api } = useFloraApi();
  const queryClient = useQueryClient();

  return useMutation({
    onSuccess: (_, { id }) =>
      Promise.all([
        queryClient.invalidateQueries({ queryKey: seedLibraryKeys.detail(id) }),
        queryClient.invalidateQueries({ queryKey: seedLibraryKeys.lists() }),
      ]),
    ...options,
    async mutationFn(variables: UpdateSeedLibraryVariables) {
      const { data } = await api.apiSeedLibrariesUuidPatch({
        ...mapToRequest(variables),
        throwOnError: true,
      });
      return mapToModel(data);
    },
  });
}
