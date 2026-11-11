import type { SeedLibrary } from '@lychen/typescript-flora-models/SeedLibrary';
import type { ApiMutationOptions } from '@lychen/vue-api-query/options';
import { useMutation, useQueryClient } from '@tanstack/vue-query';
import { useFloraApi } from '../../../client/useFloraApi';
import { seedLibraryKeys } from '../queryKeys';
import { mapToModel } from './mapToModel';
import { mapToRequest, type UpdateSeedLibraryVariables } from './mapToRequest';

/**
 * Changes a seed library, `PATCH /api/seed_libraries/{uuid}`, and resolves with it. By
 * default, it then refetches that library and the lists.
 */
export function useUpdateSeedLibrary(
  options: ApiMutationOptions<SeedLibrary, UpdateSeedLibraryVariables> = {},
) {
  const { api } = useFloraApi();
  const queryClient = useQueryClient();

  return useMutation({
    onSuccess: (_, { uuid }) =>
      Promise.all([
        queryClient.invalidateQueries({ queryKey: seedLibraryKeys.detail(uuid) }),
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
