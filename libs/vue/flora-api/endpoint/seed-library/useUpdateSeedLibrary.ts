import type {
  ApiSeedLibrariesUuidPatchData,
  ApiSeedLibrariesUuidPatchResponse,
} from '@lychen/typescript-flora-api-sdk/generated/flora-api';
import {
  optional,
  required,
  toDate,
  withoutUndefined,
} from '@lychen/typescript-api-mapping/mapping';
import type {
  SeedLibrary,
  SeedLibraryUpdateInput,
} from '@lychen/typescript-flora-models/SeedLibrary';
import type { ApiMutationOptions } from '@lychen/vue-api-query/options';
import { useApiMutation } from '@lychen/vue-api-query/useApiMutation';
import { useFloraApi } from '../../client/useFloraApi';
import { seedLibraryKeys } from './queryKeys';

export interface UpdateSeedLibraryVariables {
  uuid: string;
  /** The fields to change; those left out are kept. */
  input: SeedLibraryUpdateInput;
}

/** Changes a seed library, `PATCH /api/seed_libraries/{uuid}`, and resolves with it. */
export function useUpdateSeedLibrary(
  options: ApiMutationOptions<SeedLibrary, UpdateSeedLibraryVariables> = {},
) {
  const { api } = useFloraApi();

  return useApiMutation(
    {
      async send(variables: UpdateSeedLibraryVariables) {
        const { data } = await api.apiSeedLibrariesUuidPatch({
          ...mapToRequest(variables),
          throwOnError: true,
        });
        return mapToModel(data);
      },
      invalidates: () => [seedLibraryKeys.all],
    },
    options,
  );
}

/** The request, from the front's input, under the API's names. */
export function mapToRequest(
  variables: UpdateSeedLibraryVariables,
): Omit<ApiSeedLibrariesUuidPatchData, 'url'> {
  return {
    path: { uuid: variables.uuid },
    body: withoutUndefined({ name: variables.input.name, city: variables.input.city }),
  };
}

/**
 * The response as the front's model: list its fields one by one, under the front's names,
 * never with a spread.
 */
export function mapToModel(data: ApiSeedLibrariesUuidPatchResponse): SeedLibrary {
  return {
    uuid: required(data.uuid, 'seed library uuid'),
    name: data.name ?? '',
    city: optional(data.city),
    seedLotUuids: data.seedLots ?? [],
    createdAt: toDate(data.createdAt),
    updatedAt: toDate(data.updatedAt),
  };
}
