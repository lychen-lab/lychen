import type { AreaRequest } from '@lychen/typescript-espace-models/AreaRequest';
import type { ApiQueryOptions } from '@lychen/vue-api-query/options';
import { useItemQuery } from '@lychen/vue-api-query/useItemQuery';
import type { MaybeRefOrGetter } from 'vue';
import { useEspaceApi } from '../../client/useEspaceApi';
import { mapToModel } from './mapper';
import { areaRequestKeys } from './queryKeys';

/**
 * One area request, `GET /api/area_requests/{uuid}`. The query waits for a uuid, and fetches again
 * whenever it changes.
 */
export function useGetAreaRequest(
  uuid: MaybeRefOrGetter<string | undefined>,
  options: ApiQueryOptions<AreaRequest> = {},
) {
  const { api } = useEspaceApi();

  return useItemQuery(
    uuid,
    {
      queryKey: areaRequestKeys.detail,
      async fetch(id) {
        const { data } = await api.apiAreaRequestsUuidGet({
          path: { uuid: id },
          throwOnError: true,
        });
        return mapToModel(data);
      },
    },
    options,
  );
}
