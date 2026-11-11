import type {
  SeedLibrary,
  SeedLibraryCreateInput,
} from '@lychen/typescript-flora-models/SeedLibrary';
import type { ApiMutationOptions } from '@lychen/vue-api-query/options';
import { useSaveMutation } from '@lychen/vue-api-query/useSaveMutation';
import { useFloraApi } from '../../client/useFloraApi';
import { mapToCreateBody, mapToModel } from './mapper';
import { seedLibraryKeys } from './queryKeys';

/** `POST /api/seed_libraries`, from the front's input: resolves with the created seed library. */
export function useCreateSeedLibrary(
  options: ApiMutationOptions<SeedLibrary, SeedLibraryCreateInput> = {},
) {
  const { api } = useFloraApi();

  return useSaveMutation(
    {
      queryKeys: seedLibraryKeys,
      async save(input: SeedLibraryCreateInput) {
        const { data } = await api.apiSeedLibrariesPost({
          body: mapToCreateBody(input),
          throwOnError: true,
        });
        return mapToModel(data);
      },
      idOf: (saved) => saved.uuid,
    },
    options,
  );
}
