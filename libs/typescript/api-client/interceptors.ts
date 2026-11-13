// The interceptors of the API clients that createApiClient sets up. Their signatures are
// the part of the @hey-api/client-fetch ones they use, so they fit the client of every
// API SDK (libs/typescript/<api>-api-sdk). The bearer token, which not every API wants,
// is in bearerToken.ts.

export type RequestInterceptor = (request: Request) => Request | Promise<Request>;

export type ResponseInterceptor = (
  response: Response,
  request: Request,
) => Response | Promise<Response>;

/**
 * Gets what the call rejects with and returns what it rejects with instead. `response`
 * and `request` are undefined when the request could not be built or sent.
 */
export type ErrorInterceptor = (
  error: unknown,
  response: Response | undefined,
  request: Request | undefined,
) => unknown;

/** Each list runs in order, the next interceptor getting what the previous one returned. */
export interface ApiClientInterceptors {
  request: RequestInterceptor[];
  response: ResponseInterceptor[];
  error: ErrorInterceptor[];
}

const WRITE_CONTENT_TYPES: Partial<Record<string, string>> = {
  PATCH: 'application/merge-patch+json',
  POST: 'application/ld+json',
  PUT: 'application/ld+json',
};

/**
 * Gives a write that has no Content-Type the one API Platform reads: JSON-LD, or JSON
 * Merge Patch for a PATCH. The SDK sends each operation's own type, from the spec, but
 * the client drops it from a call without a body, and API Platform answers 415 to a write
 * it deserializes without one. A type already set is kept.
 */
export function jsonLdContentType(request: Request): Request {
  const contentType = WRITE_CONTENT_TYPES[request.method];
  if (contentType && !request.headers.has('Content-Type')) {
    request.headers.set('Content-Type', contentType);
  }
  return request;
}

/**
 * Rejects with an Error that names the request and its status, e.g.
 * "https://example.org/api/lands: 404 Not Found", with the parsed response body as its
 * cause. A failure without a response (network, request building) is passed on as is.
 */
export function httpStatusError(error: unknown, response: Response | undefined): unknown {
  if (!response) {
    return error;
  }
  return new Error(`${response.url}: ${response.status} ${response.statusText}`, {
    cause: error,
  });
}

/** The interceptors every API client starts from: new lists, safe to change. */
export function defaultInterceptors(): ApiClientInterceptors {
  return {
    request: [jsonLdContentType],
    response: [],
    error: [httpStatusError],
  };
}
