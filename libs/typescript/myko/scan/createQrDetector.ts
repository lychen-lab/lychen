import { createJsQrDetector } from './jsQrDetector';
import { createBarcodeQrDetector, type QrDetector } from './qrDetector';

export interface QrDetectorChoice {
  /** The browser's own detector: tried first, as it costs the phone's processor nothing. */
  createNative?: () => Promise<QrDetector | undefined>;
  /** The library: only where the browser has no detector. */
  createFallback?: () => Promise<QrDetector | undefined>;
}

/**
 * What reads the frames of the scanner: the browser's own `BarcodeDetector` where there is one
 * (Chrome on Android), jsQR everywhere else (Safari on iOS, Firefox), and nothing where neither
 * can be made. The library is not even loaded when the browser's detector is there.
 */
export async function createQrDetector(
  choice: QrDetectorChoice = {},
): Promise<QrDetector | undefined> {
  const {
    createNative = () => createBarcodeQrDetector(),
    createFallback = () => createJsQrDetector(),
  } = choice;

  return (await createNative()) ?? (await createFallback());
}
