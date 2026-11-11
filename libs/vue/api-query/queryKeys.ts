/**
 * The cache keys of one resource's queries: `[api, resource, endpoint, params]`, so that a
 * single call reaches a whole family. A change to the resource invalidates `all`; Mercure
 * refetching every page of a list invalidates `endpoint('get-lands')`.
 */
export interface ResourceQueryKeys {
  /** Every query of the resource, whatever the endpoint. */
  all: readonly [string, string];
  /** Every query of one endpoint, whatever its params. */
  endpoint(endpoint: string): readonly [string, string, string];
  /** One endpoint's query with these params: its composable's own key. */
  of<TParams>(endpoint: string, params: TParams): readonly [string, string, string, TParams];
}

/**
 * The cache keys of a resource of an API, e.g. `createQueryKeys('tera', 'land')`. The API's
 * name comes first, so that APIs sharing the app's QueryClient never collide.
 */
export function createQueryKeys(api: string, resource: string): ResourceQueryKeys {
  const all = [api, resource] as const;
  return {
    all,
    endpoint: (endpoint) => [...all, endpoint] as const,
    of: (endpoint, params) => [...all, endpoint, params] as const,
  };
}
