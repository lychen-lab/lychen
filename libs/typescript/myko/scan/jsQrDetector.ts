import type { QrDetector } from './qrDetector';

/** What is used of jsQR: the pixels of an image in, the text of the QR code on it (or `null`) out. */
export type JsQrDecode = (
  pixels: Uint8ClampedArray,
  width: number,
  height: number,
  options?: { inversionAttempts?: 'dontInvert' | 'onlyInvert' | 'attemptBoth' | 'invertFirst' },
) => { data: string } | null;

/** The pixels of a frame, drawn small enough for a phone to decode them several times a second. */
export interface FrameCanvas {
  /** Draws the frame at the size of the canvas and gives its pixels, or `undefined` if it cannot. */
  read(
    frame: HTMLVideoElement,
  ): { pixels: Uint8ClampedArray; width: number; height: number } | undefined;
}

export interface JsQrDetectorEnvironment {
  /** Loads jsQR. The page's own copy, loaded on first use, unless a test says otherwise. */
  loadDecoder?: () => Promise<JsQrDecode>;
  /** Where the frames are drawn: a `<canvas>` (or an `OffscreenCanvas`) of the page, unless a test says otherwise. */
  createFrameCanvas?: () => FrameCanvas | undefined;
}

/**
 * The longest side of the picture jsQR reads, in pixels. A phone's camera gives 1080p or more: at
 * that size one decoding takes longer than the scanner's 250 ms between two frames, while a label
 * held at arm's length is still hundreds of pixels wide at 640.
 */
export const JSQR_MAX_SIDE_PX = 640;

/** Loads jsQR only when a browser needs it: the library is a chunk of its own, not in the first load. */
async function loadJsQr(): Promise<JsQrDecode> {
  const module = (await import('jsqr')) as unknown as {
    default: JsQrDecode | { default: JsQrDecode };
  };
  // The package is a UMD build: depending on the tool that reads it, the function is the default
  // export, or the default export of the default export.
  const decode = typeof module.default === 'function' ? module.default : module.default.default;

  return decode;
}

function createPageFrameCanvas(): FrameCanvas | undefined {
  if (typeof document === 'undefined') {
    return undefined;
  }

  const canvas = document.createElement('canvas');
  const context = canvas.getContext('2d', { willReadFrequently: true });
  if (!context) {
    return undefined;
  }

  return {
    read(frame) {
      const sourceWidth = frame.videoWidth;
      const sourceHeight = frame.videoHeight;
      if (!sourceWidth || !sourceHeight) {
        return undefined;
      }

      const scale = Math.min(1, JSQR_MAX_SIDE_PX / Math.max(sourceWidth, sourceHeight));
      const width = Math.round(sourceWidth * scale);
      const height = Math.round(sourceHeight * scale);
      canvas.width = width;
      canvas.height = height;
      context.drawImage(frame, 0, 0, width, height);

      return { pixels: context.getImageData(0, 0, width, height).data, width, height };
    },
  };
}

/**
 * A `QrDetector` on jsQR (pure JavaScript, no browser API), for the browsers without
 * `BarcodeDetector`: Safari on iOS and Firefox. `undefined` when it cannot be made (the library does
 * not load, or the page has no canvas): the scanner then offers the manual search by label.
 *
 * Both a dark code on a light ground and the reverse are read: the labels are printed today, and
 * engraved steel will not always be darker than its code.
 */
export async function createJsQrDetector(
  environment: JsQrDetectorEnvironment = {},
): Promise<QrDetector | undefined> {
  const { loadDecoder = loadJsQr, createFrameCanvas = createPageFrameCanvas } = environment;

  let decode: JsQrDecode;
  let canvas: FrameCanvas | undefined;
  try {
    decode = await loadDecoder();
    canvas = createFrameCanvas();
  } catch {
    return undefined;
  }

  if (!canvas) {
    return undefined;
  }
  const frameCanvas = canvas;

  return {
    async detect(frame) {
      const picture = frameCanvas.read(frame);
      if (!picture) {
        return undefined;
      }

      const code = decode(picture.pixels, picture.width, picture.height, {
        inversionAttempts: 'attemptBoth',
      });

      return code?.data === undefined || code.data === '' ? undefined : code.data;
    },
  };
}
