import {
  useMercure,
  type MercureMatcher,
  type MercureUpdate,
} from '@lychen/vue-api-query/mercure/useMercure';
import type { MaybeRefOrGetter } from 'vue';
import { useEspaceApi } from '../client/useEspaceApi';

/**
 * Listens to the updates espace-api publishes on the central Mercure hub, with a fresh
 * subscriber token from `GET /api/mercure_subscription` for every connection.
 */
export function useEspaceMercure(
  matchers: MaybeRefOrGetter<MercureMatcher[]>,
  onUpdate: (update: MercureUpdate) => void,
): void {
  const { api } = useEspaceApi();
  useMercure(
    async () => (await api.apiMercureSubscriptionGet({ throwOnError: true })).data,
    matchers,
    onUpdate,
  );
}
