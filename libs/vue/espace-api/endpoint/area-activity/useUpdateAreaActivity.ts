import { useMutation, useQueryClient } from '@tanstack/vue-query';
import type {
  AreaActivity,
  AreaActivityUpdateInput,
} from '@lychen/typescript-espace/models/AreaActivity';
import { useEspaceApi } from '../../client/useEspaceApi';
import type { EspaceMutationOptions } from '../options';
import { mapToModel, mapToUpdateBody } from './mapper';
import { areaActivityKeys } from './queryKeys';

export interface UpdateAreaActivityVariables {
  uuid: string;
  /** The fields to change; those left out are kept. */
  input: AreaActivityUpdateInput;
}

/** Changes an area activity, `PATCH /api/area_activities/{uuid}`, and resolves with it. */
export function useUpdateAreaActivity(
  options: EspaceMutationOptions<AreaActivity, UpdateAreaActivityVariables> = {},
) {
  const { api } = useEspaceApi();
  const queryClient = useQueryClient();
  const { onSuccess, ...mutationOptions } = options;

  return useMutation({
    ...mutationOptions,
    async mutationFn({ uuid, input }: UpdateAreaActivityVariables) {
      const { data } = await api.apiAreaActivitiesUuidPatch({
        path: { uuid },
        body: mapToUpdateBody(input),
        throwOnError: true,
      });
      return mapToModel(data);
    },
    async onSuccess(activity, variables, onMutateResult, context) {
      // The response is the activity as GET returns it: its page needs no fetch.
      queryClient.setQueryData(areaActivityKeys.detail(activity.uuid), activity);
      await queryClient.invalidateQueries({ queryKey: areaActivityKeys.lists() });
      await onSuccess?.(activity, variables, onMutateResult, context);
    },
  });
}
