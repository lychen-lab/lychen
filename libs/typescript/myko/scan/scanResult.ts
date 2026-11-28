import { type ObjectValues } from '@lychen/typescript-utils/transformers/ObjectValues';

import { mushroomLogUuidFromQrContent } from '../qr/mushroomLogQr';

/** What a scanned QR code is, for the scanner screen: a log to open, or something else to say. */
export const SCAN_RESULT_TYPE = {
  MushroomLog: 'mushroom-log',
  /** Any other QR code: a website, a product, some text. The screen says so, clearly. */
  NotMyko: 'not-myko',
} as const;

export type ScanResultType = ObjectValues<typeof SCAN_RESULT_TYPE>;

export type ScanResult =
  | { type: typeof SCAN_RESULT_TYPE.MushroomLog; uuid: string }
  | { type: typeof SCAN_RESULT_TYPE.NotMyko; content: string };

/**
 * What the content a camera read stands for. A log's label opens its log (`uuid`, in lower case);
 * anything else is `NotMyko`, with the content as it was read, for the screen to show as text and
 * never as a link or markup.
 *
 * The origin of the label does not matter here, on purpose: a label printed from another origin
 * (staging, a laptop) is still a label, and the API answers 404 for a log that is not the
 * person's, which the log's page already handles.
 */
export function resolveScannedContent(content: string): ScanResult {
  const uuid = mushroomLogUuidFromQrContent(content);

  return uuid === undefined
    ? { type: SCAN_RESULT_TYPE.NotMyko, content }
    : { type: SCAN_RESULT_TYPE.MushroomLog, uuid };
}
