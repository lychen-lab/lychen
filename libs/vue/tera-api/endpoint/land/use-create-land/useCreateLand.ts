import type { Land, LandCreateInput } from '@lychen/typescript-tera-models/Land';
import type { ApiMutationOptions } from '@lychen/vue-api-query/options';
import { useMutation, useQueryClient } from '@tanstack/vue-query';
import { useTeraApi } from '../../../client/useTeraApi';
import { landKeys } from '../queryKeys';
import { mapToModel } from './mapToModel';
import { mapToRequest } from './mapToRequest';

/** Creates a land, `POST /api/lands`, and resolves with it. By default, it then refetches the lists. */
export function useCreateLand(options: ApiMutationOptions<Land, LandCreateInput> = {}) {
  const { api } = useTeraApi();
  const queryClient = useQueryClient();

  return useMutation({
    onSuccess: () => queryClient.invalidateQueries({ queryKey: landKeys.lists() }),
    ...options,
    async mutationFn(input: LandCreateInput) {
      const { data } = await api.apiLandsPost({ ...mapToRequest(input), throwOnError: true });
      return mapToModel(data);
    },
  });
}
