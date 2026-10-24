import { EventSource } from 'eventsource';
import { onScopeDispose, toValue, watch, type MaybeRefOrGetter } from 'vue';
import { useEspaceApi } from '../use-espace-api/useEspaceApi';

/**
 * An update the espace API pushed to the hub. A created or updated resource comes
 * whole, exactly as `GET` on its IRI returns it; a deleted one only as its `@id`.
 */
export type EspaceMercureUpdate = { '@id': string } & Record<string, unknown>;

/**
 * Selects the topics to listen to, as the Mercure 1.0 protocol does: a resource IRI
 * (`/api/area_proposals/0198…`), or, with `matchType: 'urlpattern'`, a URL pattern
 * matching a family of them (`/api/area_proposals/:uuid`).
 */
export type EspaceMercureMatcher = { match: string; matchType?: 'exact' | 'urlpattern' };

const RECONNECT_DELAY_MS = 5000;

/**
 * Listens to the updates the espace API publishes on the central Mercure hub, for as
 * long as the calling component — or effect scope — lives.
 *
 * Updates are private, so the hub only delivers those the API's token lets this user
 * receive, whatever the matchers select.
 */
export function useEspaceMercure(
  matchers: MaybeRefOrGetter<EspaceMercureMatcher[]>,
  onUpdate: (update: EspaceMercureUpdate) => void,
): void {
  const { api } = useEspaceApi();

  let source: EventSource | undefined;
  let reconnectTimer: ReturnType<typeof setTimeout> | undefined;
  let lastEventId: string | undefined;
  // Bumped by every connection attempt and by disposal, so a token that arrives after
  // the matchers changed or the component unmounted is dropped instead of used.
  let generation = 0;

  function disconnect() {
    clearTimeout(reconnectTimer);
    source?.close();
    source = undefined;
  }

  async function connect() {
    const current = ++generation;
    disconnect();

    const selected = toValue(matchers);
    if (selected.length === 0) {
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
    for (const { match, matchType } of selected) {
      url.searchParams.append(matchType === 'urlpattern' ? 'match_urlpattern' : 'match', match);
    }
    if (lastEventId) {
      // Replays whatever was published while this client was reconnecting.
      url.searchParams.set('last_event_id', lastEventId);
    }

    // The hub takes the token from the Authorization header only — never from the URL,
    // where it would leak (RFC 9700) — and a native EventSource cannot set headers. This
    // one streams over fetch, which can; the alternative, a cookie, cannot reach a hub on
    // another domain than the API's.
    const { token } = subscription;
    const eventSource = new EventSource(url, {
      fetch: (input, init) =>
        fetch(input, { ...init, headers: { ...init.headers, Authorization: `Bearer ${token}` } }),
    });
    eventSource.onmessage = (event: MessageEvent<string>) => {
      lastEventId = event.lastEventId;
      onUpdate(JSON.parse(event.data) as EspaceMercureUpdate);
    };
    eventSource.onerror = () => {
      // Dropped connections are retried on their own, resuming from the last event. A
      // *closed* source means the hub turned the request down — an expired token,
      // typically — so start over with a new one.
      if (eventSource.readyState === EventSource.CLOSED) {
        reconnectTimer = setTimeout(connect, RECONNECT_DELAY_MS);
      }
    };
    source = eventSource;
  }

  watch(() => toValue(matchers), connect, { immediate: true, deep: true });

  onScopeDispose(() => {
    generation++;
    disconnect();
  });
}
