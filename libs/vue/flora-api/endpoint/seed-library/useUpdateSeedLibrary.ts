import type {
  SeedLibrary,
  SeedLibraryUpdateInput,
} from '@lychen/typescript-flora-models/SeedLibrary';
import type { ApiMutationOptions } from '@lychen/vue-api-query/options';
import { useSaveMutation } from '@lychen/vue-api-query/useSaveMutation';
import { useFloraApi } from '../../client/useFloraApi';
import { mapToModel, mapToUpdateBody } from './mapper';
import { seedLibraryKeys } from './queryKeys';

export interface UpdateSeedLibraryVariables {
  uuid: string;
  /** The fields to change; those left out are kept. */
  input: SeedLibraryUpdateInput;
}

/** `PATCH /api/seed_libraries/{uuid}`, from the fields to change: resolves with the changed seed library. */
export function useUpdateSeedLibrary(
  options: ApiMutationOptions<SeedLibrary, UpdateSeedLibraryVariables> = {},
) {
  const { api } = useFloraApi();

  return useSaveMutation(
    {
      queryKeys: seedLibraryKeys,
      async save({ uuid, input }: UpdateSeedLibraryVariables) {
        const { data } = await api.apiSeedLibrariesUuidPatch({
          path: { uuid },
          body: mapToUpdateBody(input),
          throwOnError: true,
        });
        return mapToModel(data);
      },
      idOf: (saved) => saved.uuid,
    },
    options,
  );
}
