import { onScopeDispose, toValue, watch, type MaybeRefOrGetter } from 'vue';
import { useEspaceApi } from '../use-espace-api/useEspaceApi';

/**
 * An update the espace API pushed to the hub. A created or updated resource comes
 * whole, exactly as `GET` on its IRI returns it; a deleted one only as its `@id`.
 */
export type EspaceMercureUpdate = { '@id': string } & Record<string, unknown>;

const RECONNECT_DELAY_MS = 5000;

/**
 * Listens to the updates the espace API publishes on the central Mercure hub, for as
 * long as the calling component — or effect scope — lives.
 *
 * `topics` are Mercure topic selectors: a resource IRI (`/api/area_proposals/0198…`),
 * or a URI template matching all of them (`/api/area_proposals/{uuid}`). Updates are
 * private, so the hub only delivers those the API's token lets this user receive.
 */
export function useEspaceMercure(
  topics: MaybeRefOrGetter<string[]>,
  onUpdate: (update: EspaceMercureUpdate) => void,
): void {
  const { api } = useEspaceApi();

  let source: EventSource | undefined;
  let reconnectTimer: ReturnType<typeof setTimeout> | undefined;
  let lastEventId: string | undefined;
  // Bumped by every connection attempt and by disposal, so a token that arrives after
  // the topics changed or the component unmounted is dropped instead of used.
  let generation = 0;

  function disconnect() {
    clearTimeout(reconnectTimer);
    source?.close();
    source = undefined;
  }

  async function connect() {
    const current = ++generation;
    disconnect();

    const selectors = toValue(topics);
    if (selectors.length === 0) {
      return;
    }

    // Subscriber tokens are short-lived: fetch a fresh one for every connection.
    const subscription = await api.GET('/api/mercure_subscription').then(
      ({ data }) => data,
      () => undefined,
    );
    if (current !== generation) {
      return;
    }
    if (!subscription?.hubUrl || !subscription.token) {
      reconnectTimer = setTimeout(connect, RECONNECT_DELAY_MS);
      return;
    }

    const url = new URL(subscription.hubUrl);
    for (const topic of selectors) {
      url.searchParams.append('topic', topic);
    }
    // EventSource cannot set an Authorization header: the hub reads the token from this
    // query parameter instead, and redacts it from its access logs.
    url.searchParams.set('authorization', subscription.token);
    if (lastEventId) {
      // Replays whatever was published while this client was reconnecting.
      url.searchParams.set('lastEventID', lastEventId);
    }

    const eventSource = new EventSource(url);
    eventSource.onmessage = (event: MessageEvent<string>) => {
      lastEventId = event.lastEventId;
      onUpdate(JSON.parse(event.data) as EspaceMercureUpdate);
    };
    eventSource.onerror = () => {
      // Dropped connections are retried by the browser on its own. A *closed* source
      // means the hub turned the request down — an expired token, typically — so start
      // over with a new one.
      if (eventSource.readyState === EventSource.CLOSED) {
        reconnectTimer = setTimeout(connect, RECONNECT_DELAY_MS);
      }
    };
    source = eventSource;
  }

  watch(() => toValue(topics), connect, { immediate: true, deep: true });

  onScopeDispose(() => {
    generation++;
    disconnect();
  });
}
