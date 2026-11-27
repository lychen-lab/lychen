/**
 * What a log's QR code holds: `<app origin>/l/<log uuid>`.
 *
 * THIS FORMAT MUST NEVER CHANGE. Labels are printed with it, and will later be engraved on steel:
 * a label already out there has to open its log for as long as the log exists. This file is the one
 * place that writes it and reads it; `mushroomLogQr.test.ts` pins it.
 */
const QR_PATH_PREFIX = '/l/';

const UUID = /^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i;

/**
 * The content of the QR code of a log: the app's origin (`https://myko.example`, or
 * `http://localhost:5820`; a trailing slash or a path is ignored) and the log's uuid.
 */
export function mushroomLogQrContent(appOrigin: string, logUuid: string): string {
  return `${new URL(appOrigin).origin}${QR_PATH_PREFIX}${logUuid}`;
}

/**
 * The uuid of the log a QR code opens, or `undefined` when the content is not a Myko label: not an
 * http(s) address, another path, or something other than a uuid after `/l/`. The uuid comes back
 * in lower case, as the API gives it (a scanner may read the code in upper case).
 *
 * With `appOrigin`, a label of another origin (a staging label scanned by the production app) is not
 * one either: leave it out to accept any origin.
 */
export function mushroomLogUuidFromQrContent(
  content: string,
  appOrigin?: string,
): string | undefined {
  let url: URL;
  try {
    url = new URL(content.trim());
  } catch {
    return undefined;
  }

  if (url.protocol !== 'http:' && url.protocol !== 'https:') {
    return undefined;
  }

  if (appOrigin !== undefined && url.origin !== new URL(appOrigin).origin) {
    return undefined;
  }

  if (!url.pathname.startsWith(QR_PATH_PREFIX)) {
    return undefined;
  }

  const uuid = url.pathname.slice(QR_PATH_PREFIX.length).replace(/\/$/, '');

  return UUID.test(uuid) ? uuid.toLowerCase() : undefined;
}
