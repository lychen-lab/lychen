import { encode } from 'uqr';
import { describe, expect, it, vi } from 'vitest';

import { mushroomLogQrContent } from '../qr/mushroomLogQr';
import {
  createJsQrDetector,
  type FrameCanvas,
  type JsQrDecode,
  JSQR_MAX_SIDE_PX,
} from './jsQrDetector';

const FRAME = {} as HTMLVideoElement;
const PIXELS = { pixels: new Uint8ClampedArray(4), width: 1, height: 1 };

function fakeCanvas(picture: ReturnType<FrameCanvas['read']> = PIXELS) {
  const read = vi.fn(() => picture);

  return { canvas: { read } satisfies FrameCanvas, read };
}

describe('createJsQrDetector (with a fake decoder)', () => {
  it('has no detector when the library does not load', async () => {
    const detector = await createJsQrDetector({
      loadDecoder: async () => {
        throw new Error('Failed to fetch dynamically imported module');
      },
      createFrameCanvas: () => fakeCanvas().canvas,
    });

    expect(detector).toBeUndefined();
  });

  it('has no detector when the page has no canvas', async () => {
    const detector = await createJsQrDetector({
      loadDecoder: async () => () => null,
      createFrameCanvas: () => undefined,
    });

    expect(detector).toBeUndefined();
  });

  it('reads the content of the code on the frame, in either polarity', async () => {
    const decode = vi.fn<JsQrDecode>(() => ({ data: 'https://myko.example/l/abc' }));
    const { canvas, read } = fakeCanvas();
    const detector = await createJsQrDetector({
      loadDecoder: async () => decode,
      createFrameCanvas: () => canvas,
    });

    expect(await detector?.detect(FRAME)).toBe('https://myko.example/l/abc');
    expect(read).toHaveBeenCalledWith(FRAME);
    expect(decode).toHaveBeenCalledWith(PIXELS.pixels, 1, 1, { inversionAttempts: 'attemptBoth' });
  });

  it('says there is nothing when no code is on the frame, or the code is empty', async () => {
    const none = await createJsQrDetector({
      loadDecoder: async () => () => null,
      createFrameCanvas: () => fakeCanvas().canvas,
    });
    const empty = await createJsQrDetector({
      loadDecoder: async () => () => ({ data: '' }),
      createFrameCanvas: () => fakeCanvas().canvas,
    });

    expect(await none?.detect(FRAME)).toBeUndefined();
    expect(await empty?.detect(FRAME)).toBeUndefined();
  });

  it('does not decode a frame that could not be drawn (the camera has not started yet)', async () => {
    const decode = vi.fn<JsQrDecode>(() => ({ data: 'never' }));
    const detector = await createJsQrDetector({
      loadDecoder: async () => decode,
      createFrameCanvas: () => ({ read: () => undefined }),
    });

    expect(await detector?.detect(FRAME)).toBeUndefined();
    expect(decode).not.toHaveBeenCalled();
  });

  it('makes the library and the canvas once, not at every frame', async () => {
    const loadDecoder = vi.fn(async () => (() => null) as JsQrDecode);
    const createFrameCanvas = vi.fn(() => fakeCanvas().canvas);
    const detector = await createJsQrDetector({ loadDecoder, createFrameCanvas });

    await detector?.detect(FRAME);
    await detector?.detect(FRAME);

    expect(loadDecoder).toHaveBeenCalledTimes(1);
    expect(createFrameCanvas).toHaveBeenCalledTimes(1);
  });

  it('keeps the picture jsQR reads small enough to decode between two frames', () => {
    expect(JSQR_MAX_SIDE_PX).toBeLessThanOrEqual(800);
    expect(JSQR_MAX_SIDE_PX).toBeGreaterThanOrEqual(480);
  });
});

/**
 * The pixels of a QR code as a camera would give them: `scale` pixels a module, four modules of
 * quiet zone, light ground and dark modules (or the reverse for `inverted`), as RGBA.
 */
function renderQrPixels(content: string, options: { scale: number; inverted?: boolean }) {
  const { data, size } = encode(content, { ecc: 'M', border: 4 });
  const width = size * options.scale;
  const pixels = new Uint8ClampedArray(width * width * 4);

  for (let y = 0; y < width; y++) {
    for (let x = 0; x < width; x++) {
      const dark = data[Math.floor(y / options.scale)]?.[Math.floor(x / options.scale)] === true;
      const value = dark !== (options.inverted ?? false) ? 0 : 255;
      pixels.set([value, value, value, 255], (y * width + x) * 4);
    }
  }

  return { pixels, width, height: width };
}

// The real jsQR, on the codes the app prints: the one test that proves the library reads what
// `mushroomLogQrCode` draws, and not only that the wiring around it is right.
describe('createJsQrDetector (with jsQR)', () => {
  const content = mushroomLogQrContent(
    'https://myko.example',
    '0198a2b4-7c1e-7d3a-9f10-5b6c7d8e9f01',
  );

  it('reads a printed label', async () => {
    const detector = await createJsQrDetector({
      createFrameCanvas: () => fakeCanvas(renderQrPixels(content, { scale: 6 })).canvas,
    });

    expect(await detector?.detect(FRAME)).toBe(content);
  });

  it('reads a light code on a dark ground', async () => {
    const detector = await createJsQrDetector({
      createFrameCanvas: () =>
        fakeCanvas(renderQrPixels(content, { scale: 6, inverted: true })).canvas,
    });

    expect(await detector?.detect(FRAME)).toBe(content);
  });

  it('reads a code shown small, as at arm length', async () => {
    const detector = await createJsQrDetector({
      createFrameCanvas: () => fakeCanvas(renderQrPixels(content, { scale: 3 })).canvas,
    });

    expect(await detector?.detect(FRAME)).toBe(content);
  });

  it('finds nothing on a blank frame', async () => {
    const blank = {
      pixels: new Uint8ClampedArray(200 * 200 * 4).fill(255),
      width: 200,
      height: 200,
    };
    const detector = await createJsQrDetector({
      createFrameCanvas: () => fakeCanvas(blank).canvas,
    });

    expect(await detector?.detect(FRAME)).toBeUndefined();
  });
});
