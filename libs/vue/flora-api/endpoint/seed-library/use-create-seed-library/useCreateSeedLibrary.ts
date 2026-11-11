import type {
  SeedLibrary,
  SeedLibraryCreateInput,
} from '@lychen/typescript-flora-models/SeedLibrary';
import type { ApiMutationOptions } from '@lychen/vue-api-query/options';
import { useMutation, useQueryClient } from '@tanstack/vue-query';
import { useFloraApi } from '../../../client/useFloraApi';
import { seedLibraryKeys } from '../queryKeys';
import { mapToModel } from './mapToModel';
import { mapToRequest } from './mapToRequest';

/**
 * Creates a seed library, `POST /api/seed_libraries`, and resolves with it. By default, it
 * then refetches the lists.
 */
export function useCreateSeedLibrary(
  options: ApiMutationOptions<SeedLibrary, SeedLibraryCreateInput> = {},
) {
  const { api } = useFloraApi();
  const queryClient = useQueryClient();

  return useMutation({
    onSuccess: () => queryClient.invalidateQueries({ queryKey: seedLibraryKeys.lists() }),
    ...options,
    async mutationFn(input: SeedLibraryCreateInput) {
      const { data } = await api.apiSeedLibrariesPost({
        ...mapToRequest(input),
        throwOnError: true,
      });
      return mapToModel(data);
    },
  });
}
