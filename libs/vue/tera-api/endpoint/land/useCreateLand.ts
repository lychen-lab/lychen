import type { Land, LandCreateInput } from '@lychen/typescript-tera-models/Land';
import type { ApiMutationOptions } from '@lychen/vue-api-query/options';
import { useSaveMutation } from '@lychen/vue-api-query/useSaveMutation';
import { useTeraApi } from '../../client/useTeraApi';
import { mapToCreateBody, mapToModel } from './mapper';
import { landKeys } from './queryKeys';

/** `POST /api/lands`, from the front's input: resolves with the created land. */
export function useCreateLand(options: ApiMutationOptions<Land, LandCreateInput> = {}) {
  const { api } = useTeraApi();

  return useSaveMutation(
    {
      queryKeys: landKeys,
      async save(input: LandCreateInput) {
        const { data } = await api.apiLandsPost({
          body: mapToCreateBody(input),
          throwOnError: true,
        });
        return mapToModel(data);
      },
      idOf: (saved) => saved.ulid,
    },
    options,
  );
}
