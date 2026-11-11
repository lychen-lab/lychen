import { useQuery, type QueryKey } from '@tanstack/vue-query';
import { computed, toValue, type MaybeRefOrGetter } from 'vue';
import type { AreaRequest } from '@lychen/typescript-espace/models/AreaRequest';
import { useEspaceApi } from '../../client/useEspaceApi';
import { required } from '../mapping';
import type { EspaceQueryOptions } from '../options';
import { mapToModel } from './mapper';
import { areaRequestKeys } from './queryKeys';

/**
 * One area request, `GET /api/area_requests/{uuid}`. The query waits for a uuid, and
 * fetches again whenever it changes.
 */
export function useGetAreaRequest(
  uuid: MaybeRefOrGetter<string | undefined>,
  options: EspaceQueryOptions<AreaRequest> = {},
) {
  const { api } = useEspaceApi();
  const { enabled = true, ...queryOptions } = options;

  return useQuery<AreaRequest, Error, AreaRequest, QueryKey>({
    ...queryOptions,
    queryKey: computed(() => areaRequestKeys.detail(toValue(uuid) ?? '')),
    async queryFn() {
      const { data } = await api.apiAreaRequestsUuidGet({
        path: { uuid: required(toValue(uuid), 'uuid to fetch the area request of') },
        throwOnError: true,
      });
      return mapToModel(data);
    },
    enabled: computed(() => Boolean(toValue(uuid)) && toValue(enabled)),
  });
}
