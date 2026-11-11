import type { Land, LandUpdateInput } from '@lychen/typescript-tera-models/Land';
import type { ApiMutationOptions } from '@lychen/vue-api-query/options';
import { useSaveMutation } from '@lychen/vue-api-query/useSaveMutation';
import { useTeraApi } from '../../client/useTeraApi';
import { mapToModel, mapToUpdateBody } from './mapper';
import { landKeys } from './queryKeys';

export interface UpdateLandVariables {
  ulid: string;
  /** The fields to change; those left out are kept. */
  input: LandUpdateInput;
}

/** `PATCH /api/lands/{ulid}`, from the fields to change: resolves with the changed land. */
export function useUpdateLand(options: ApiMutationOptions<Land, UpdateLandVariables> = {}) {
  const { api } = useTeraApi();

  return useSaveMutation(
    {
      queryKeys: landKeys,
      async save({ ulid, input }: UpdateLandVariables) {
        const { data } = await api.apiLandsUlidPatch({
          path: { ulid },
          body: mapToUpdateBody(input),
          throwOnError: true,
        });
        return mapToModel(data);
      },
      idOf: (saved) => saved.ulid,
    },
    options,
  );
}
