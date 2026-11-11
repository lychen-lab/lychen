import type {
  AreaActivity,
  AreaActivityCreateInput,
} from '@lychen/typescript-espace-models/AreaActivity';
import type { ApiMutationOptions } from '@lychen/vue-api-query/options';
import { useMutation, useQueryClient } from '@tanstack/vue-query';
import { useEspaceApi } from '../../../client/useEspaceApi';
import { areaActivityKeys } from '../queryKeys';
import { mapToModel } from './mapToModel';
import { mapToRequest } from './mapToRequest';

/**
 * Creates an area activity, `POST /api/area_activities`, and resolves with it. By default, it then refetches the
 * lists.
 */
export function useCreateAreaActivity(
  options: ApiMutationOptions<AreaActivity, AreaActivityCreateInput> = {},
) {
  const { api } = useEspaceApi();
  const queryClient = useQueryClient();

  return useMutation({
    onSuccess: () => queryClient.invalidateQueries({ queryKey: areaActivityKeys.lists() }),
    ...options,
    async mutationFn(input: AreaActivityCreateInput) {
      const { data } = await api.apiAreaActivitiesPost({
        ...mapToRequest(input),
        throwOnError: true,
      });
      return mapToModel(data);
    },
  });
}
