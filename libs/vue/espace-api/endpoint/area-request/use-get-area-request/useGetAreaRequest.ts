import type { AreaRequest } from '@lychen/typescript-espace-models/AreaRequest';
import type { ApiQueryOptions } from '@lychen/vue-api-query/options';
import { useQuery, type QueryKey } from '@tanstack/vue-query';
import { computed, toValue, type MaybeRefOrGetter } from 'vue';
import { useEspaceApi } from '../../../client/useEspaceApi';
import { areaRequestKeys } from '../queryKeys';
import { mapToModel } from './mapToModel';

/**
 * One area request, `GET /api/area_requests/{uuid}`. The query waits for a uuid, and fetches again
 * whenever it changes.
 */
export function useGetAreaRequest(
  uuid: MaybeRefOrGetter<string | undefined>,
  options: ApiQueryOptions<AreaRequest> = {},
) {
  const { api } = useEspaceApi();
  const { enabled = true, ...queryOptions } = options;

  return useQuery<AreaRequest, Error, AreaRequest, QueryKey>({
    queryKey: computed(() => areaRequestKeys.detail(toValue(uuid) ?? '')),
    ...queryOptions,
    enabled: computed(() => Boolean(toValue(uuid)) && toValue(enabled)),
    async queryFn() {
      const current = toValue(uuid);
      if (!current) {
        throw new Error('useGetAreaRequest: there is no uuid to fetch yet.');
      }
      const { data } = await api.apiAreaRequestsUuidGet({
        path: { uuid: current },
        throwOnError: true,
      });
      return mapToModel(data);
    },
  });
}
