/**
 * Whether a failed API call answered 404. `useEspaceApi` rejects with an Error whose cause
 * is the parsed response body: API Platform's error document, which carries the status.
 */
export function isNotFoundError(error: unknown): boolean {
  if (!(error instanceof Error) || typeof error.cause !== 'object' || error.cause === null) {
    return false;
  }
  return 'status' in error.cause && error.cause.status === 404;
}
