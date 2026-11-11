import {
  defaultInterceptors,
  type ApiClientInterceptors,
  type ErrorInterceptor,
  type RequestInterceptor,
  type ResponseInterceptor,
} from './interceptors';

/** The part of an API SDK's client (generated/<api>-api/client) that createApiClient sets up. */
export interface ApiClient {
  interceptors: {
    request: { use(fn: RequestInterceptor): unknown };
    response: { use(fn: ResponseInterceptor): unknown };
    error: { use(fn: ErrorInterceptor): unknown };
  };
}

/** The options of an SDK's client that createApiClient sets. */
export interface ApiClientConfig {
  baseUrl?: string;
  throwOnError?: boolean;
}

export interface ApiClientOptions<Config extends ApiClientConfig> {
  /** The API's base URL, e.g. `import.meta.env.VITE_API_URL`. */
  baseUrl: string;
  /** Any other option of the SDK's client, over the defaults (`throwOnError: true`). */
  config?: Omit<Config, 'baseUrl'>;
  /**
   * The API's own interceptors, from the defaults every API shares (defaultInterceptors).
   * Return the lists that change, a list left out keeps its defaults:
   * - complete: `(defaults) => ({ request: [...defaults.request, addLocale] })`;
   * - drop or replace one:
   *   `(defaults) => ({ error: defaults.error.filter((fn) => fn !== httpStatusError) })`.
   */
  interceptors?: (defaults: ApiClientInterceptors) => Partial<ApiClientInterceptors>;
}

/**
 * Creates the client an API SDK's class runs on, set up like every other API's:
 *
 *   const api = new TeraApi({
 *     client: createApiClient(createClient, { baseUrl: import.meta.env.VITE_API_URL }),
 *   });
 *
 * `createClient` is the SDK's own (generated/<api>-api/client). The client rejects on any
 * non-2xx response, as callers (and Tanstack Query) expect, and runs the default
 * interceptors unless `options.interceptors` changes them.
 */
export function createApiClient<Config extends ApiClientConfig, Client extends ApiClient>(
  // Optional, like the SDK's: Config is then inferred from it alone, not `Config | undefined`.
  createClient: (config?: Config) => Client,
  { baseUrl, config, interceptors }: ApiClientOptions<NoInfer<Config>>,
): Client {
  // Config is the SDK's client options, which all are optional: these fit it.
  const client = createClient({ throwOnError: true, ...config, baseUrl } as Config);

  const defaults = defaultInterceptors();
  const { request, response, error } = { ...defaults, ...interceptors?.(defaults) };
  for (const fn of request) {
    client.interceptors.request.use(fn);
  }
  for (const fn of response) {
    client.interceptors.response.use(fn);
  }
  for (const fn of error) {
    client.interceptors.error.use(fn);
  }

  return client;
}
