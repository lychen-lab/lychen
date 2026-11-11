import type {
  ApiSeedLibrariesPostData,
  ApiSeedLibrariesPostResponse,
} from '@lychen/typescript-flora-api-sdk/generated/flora-api';
import {
  optional,
  required,
  toDate,
  withoutUndefined,
} from '@lychen/typescript-api-mapping/mapping';
import type {
  SeedLibrary,
  SeedLibraryCreateInput,
} from '@lychen/typescript-flora-models/SeedLibrary';
import type { ApiMutationOptions } from '@lychen/vue-api-query/options';
import { useApiMutation } from '@lychen/vue-api-query/useApiMutation';
import { useFloraApi } from '../../client/useFloraApi';
import { seedLibraryKeys } from './queryKeys';

/** What the creation form gives. */
export type CreateSeedLibraryVariables = SeedLibraryCreateInput;

/** Creates a seed library, `POST /api/seed_libraries`, and resolves with it. */
export function useCreateSeedLibrary(
  options: ApiMutationOptions<SeedLibrary, CreateSeedLibraryVariables> = {},
) {
  const { api } = useFloraApi();

  return useApiMutation(
    {
      async send(variables: CreateSeedLibraryVariables) {
        const { data } = await api.apiSeedLibrariesPost({
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
  variables: CreateSeedLibraryVariables,
): Omit<ApiSeedLibrariesPostData, 'url'> {
  return {
    body: withoutUndefined({ name: variables.name, city: variables.city }),
  };
}

/**
 * The response as the front's model: list its fields one by one, under the front's names,
 * never with a spread.
 */
export function mapToModel(data: ApiSeedLibrariesPostResponse): SeedLibrary {
  return {
    uuid: required(data.uuid, 'seed library uuid'),
    name: data.name ?? '',
    city: optional(data.city),
    seedLotUuids: data.seedLots ?? [],
    createdAt: toDate(data.createdAt),
    updatedAt: toDate(data.updatedAt),
  };
}
