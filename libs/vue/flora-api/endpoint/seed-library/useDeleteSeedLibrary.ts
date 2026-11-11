import type { ApiSeedLibrariesUuidDeleteData } from '@lychen/typescript-flora-api-sdk/generated/flora-api';
import type { ApiMutationOptions } from '@lychen/vue-api-query/options';
import { useApiMutation } from '@lychen/vue-api-query/useApiMutation';
import { useFloraApi } from '../../client/useFloraApi';
import { seedLibraryKeys } from './queryKeys';
import { getSeedLibraryQueryKey } from './useGetSeedLibrary';

/** The library's uuid. */
export type DeleteSeedLibraryVariables = string;

/** Deletes a seed library, `DELETE /api/seed_libraries/{uuid}`, given its uuid. */
export function useDeleteSeedLibrary(
  options: ApiMutationOptions<void, DeleteSeedLibraryVariables> = {},
) {
  const { api } = useFloraApi();

  return useApiMutation(
    {
      async send(variables: DeleteSeedLibraryVariables) {
        await api.apiSeedLibrariesUuidDelete({ ...mapToRequest(variables), throwOnError: true });
      },
      // Nothing left to fetch: a refetch would only fail.
      update: (queryClient, _, uuid) =>
        queryClient.removeQueries({ queryKey: getSeedLibraryQueryKey(uuid) }),
      invalidates: () => [seedLibraryKeys.all],
    },
    options,
  );
}

/** The request, from the front's input, under the API's names. */
export function mapToRequest(
  variables: DeleteSeedLibraryVariables,
): Omit<ApiSeedLibrariesUuidDeleteData, 'url'> {
  return {
    path: { uuid: variables },
  };
}
