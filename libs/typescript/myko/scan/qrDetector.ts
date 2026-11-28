/** Reads the QR code on a video frame, whatever does the reading. */
export interface QrDetector {
  /** The content of the QR code on the frame, or `undefined` when there is none. Never empty text. */
  detect(frame: HTMLVideoElement): Promise<string | undefined>;
}

/**
 * The part of the browser's `BarcodeDetector` (Shape Detection API) that is used: TypeScript's DOM
 * types do not have it, and Chrome on Android has it while Safari on iOS and Firefox do not.
 */
interface BarcodeDetectorLike {
  detect(frame: HTMLVideoElement): Promise<{ rawValue: string }[]>;
}

interface BarcodeDetectorConstructorLike {
  new (options?: { formats?: string[] }): BarcodeDetectorLike;
  getSupportedFormats?: () => Promise<string[]>;
}

const QR_FORMAT = 'qr_code';

/**
 * A `QrDetector` on the browser's own `BarcodeDetector`, or `undefined` when this browser has none
 * that reads QR codes (the scanner then needs another detector, or the manual search by label).
 * `scope` is where to look for it: the page's globals unless a test says otherwise.
 */
export async function createBarcodeQrDetector(
  scope: { BarcodeDetector?: BarcodeDetectorConstructorLike } = globalThis as typeof globalThis & {
    BarcodeDetector?: BarcodeDetectorConstructorLike;
  },
): Promise<QrDetector | undefined> {
  const Detector = scope.BarcodeDetector;

  if (!Detector) {
    return undefined;
  }

  let detector: BarcodeDetectorLike;
  try {
    // Not every build of the API says which formats it reads: the constructor is the judge.
    const formats = (await Detector.getSupportedFormats?.()) ?? [QR_FORMAT];
    if (!formats.includes(QR_FORMAT)) {
      return undefined;
    }
    detector = new Detector({ formats: [QR_FORMAT] });
  } catch {
    return undefined;
  }

  return {
    async detect(frame) {
      const codes = await detector.detect(frame);

      return codes.find((code) => code.rawValue !== '')?.rawValue;
    },
  };
}
