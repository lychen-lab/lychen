/**
 * The origin a log's QR code points at: the one the printed labels must open for good, which is not
 * necessarily the one the app runs on (a staging build, a laptop on `https://localhost:5820`, an
 * installed PWA behind another host name).
 *
 * `configured` is the deployment's value (`VITE_MYKO_APP_URL`). It is only used when it is an http(s)
 * address; anything else (unset, empty, blanks, the `${MYKO_APP_URL}` an image's entrypoint leaves
 * when the variable is not in the container's environment, a path-only value, `javascript:…`) falls
 * back to `currentOrigin`, so that a missing value never breaks the app or its tests.
 */
export function resolveMushroomLogQrOrigin(
  configured: string | undefined,
  currentOrigin: string,
): string {
  return httpOrigin(configured) ?? new URL(currentOrigin).origin;
}

function httpOrigin(value: string | undefined): string | undefined {
  const trimmed = value?.trim();

  if (!trimmed) {
    return undefined;
  }

  let url: URL;
  try {
    url = new URL(trimmed);
  } catch {
    return undefined;
  }

  return url.protocol === 'http:' || url.protocol === 'https:' ? url.origin : undefined;
}
