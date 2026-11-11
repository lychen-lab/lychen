import type { ApiLandsUlidDeleteData } from '@lychen/typescript-tera-api-sdk/generated/tera-api';
import type { ApiMutationOptions } from '@lychen/vue-api-query/options';
import { useApiMutation } from '@lychen/vue-api-query/useApiMutation';
import { useTeraApi } from '../../client/useTeraApi';
import { landKeys } from './queryKeys';
import { getLandQueryKey } from './useGetLand';

/** The land's ulid. */
export type DeleteLandVariables = string;

/** Deletes a land, `DELETE /api/lands/{ulid}`, given its ulid. */
export function useDeleteLand(options: ApiMutationOptions<void, DeleteLandVariables> = {}) {
  const { api } = useTeraApi();

  return useApiMutation(
    {
      async send(variables: DeleteLandVariables) {
        await api.apiLandsUlidDelete({ ...mapToRequest(variables), throwOnError: true });
      },
      // Nothing left to fetch: a refetch would only fail.
      update: (queryClient, _, ulid) =>
        queryClient.removeQueries({ queryKey: getLandQueryKey(ulid) }),
      invalidates: () => [landKeys.all],
    },
    options,
  );
}

/** The request, from the front's input, under the API's names. */
export function mapToRequest(variables: DeleteLandVariables): Omit<ApiLandsUlidDeleteData, 'url'> {
  return {
    path: { ulid: variables },
  };
}
