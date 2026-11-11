import { useMutation, useQueryClient } from '@tanstack/vue-query';
import type {
  AreaActivity,
  AreaActivityCreateInput,
} from '@lychen/typescript-espace/models/AreaActivity';
import { useEspaceApi } from '../../client/useEspaceApi';
import type { EspaceMutationOptions } from '../options';
import { mapToCreateBody, mapToModel } from './mapper';
import { areaActivityKeys } from './queryKeys';

/** Creates an area activity, `POST /api/area_activities`, and resolves with it. */
export function useCreateAreaActivity(
  options: EspaceMutationOptions<AreaActivity, AreaActivityCreateInput> = {},
) {
  const { api } = useEspaceApi();
  const queryClient = useQueryClient();
  const { onSuccess, ...mutationOptions } = options;

  return useMutation({
    ...mutationOptions,
    async mutationFn(input: AreaActivityCreateInput) {
      const { data } = await api.apiAreaActivitiesPost({
        body: mapToCreateBody(input),
        throwOnError: true,
      });
      return mapToModel(data);
    },
    async onSuccess(activity, input, onMutateResult, context) {
      // The response is the activity as GET returns it: its page needs no fetch.
      queryClient.setQueryData(areaActivityKeys.detail(activity.uuid), activity);
      await queryClient.invalidateQueries({ queryKey: areaActivityKeys.lists() });
      await onSuccess?.(activity, input, onMutateResult, context);
    },
  });
}
