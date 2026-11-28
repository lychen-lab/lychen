import { describe, expect, it, vi } from 'vitest';

import { createBarcodeQrDetector } from './qrDetector';

const FRAME = {} as HTMLVideoElement;

function fakeBarcodeDetector(options: {
  formats?: string[] | 'unknown';
  codes?: { rawValue: string }[];
  throwOnConstruct?: boolean;
}) {
  const detect = vi.fn(async () => options.codes ?? []);
  const constructed = vi.fn();
  const formats =
    options.formats === 'unknown' ? undefined : (options.formats ?? ['qr_code', 'ean_13']);

  class FakeBarcodeDetector {
    constructor(constructorOptions?: { formats?: string[] }) {
      if (options.throwOnConstruct) {
        throw new Error('unsupported format');
      }
      constructed(constructorOptions);
    }

    detect = detect;

    static getSupportedFormats = formats === undefined ? undefined : async () => formats;
  }

  return { BarcodeDetector: FakeBarcodeDetector, detect, constructed };
}

describe('createBarcodeQrDetector', () => {
  it('has no detector where the browser has no BarcodeDetector', async () => {
    expect(await createBarcodeQrDetector({})).toBeUndefined();
  });

  it('has no detector where BarcodeDetector does not read QR codes', async () => {
    const fake = fakeBarcodeDetector({ formats: ['ean_13', 'code_128'] });

    expect(await createBarcodeQrDetector(fake)).toBeUndefined();
    expect(fake.constructed).not.toHaveBeenCalled();
  });

  it('asks only for QR codes', async () => {
    const fake = fakeBarcodeDetector({});

    await createBarcodeQrDetector(fake);

    expect(fake.constructed).toHaveBeenCalledWith({ formats: ['qr_code'] });
  });

  it('lets the constructor judge when the browser does not list its formats', async () => {
    const fake = fakeBarcodeDetector({ formats: 'unknown' });

    expect(await createBarcodeQrDetector(fake)).toBeDefined();
    expect(fake.constructed).toHaveBeenCalledWith({ formats: ['qr_code'] });
  });

  it('has no detector when the constructor refuses', async () => {
    expect(
      await createBarcodeQrDetector(fakeBarcodeDetector({ throwOnConstruct: true })),
    ).toBeUndefined();
  });

  it('reads the content of the first code on the frame', async () => {
    const fake = fakeBarcodeDetector({ codes: [{ rawValue: 'first' }, { rawValue: 'second' }] });
    const detector = await createBarcodeQrDetector(fake);

    expect(await detector?.detect(FRAME)).toBe('first');
    expect(fake.detect).toHaveBeenCalledWith(FRAME);
  });

  it('says there is nothing when no code is on the frame, or the code is empty', async () => {
    const none = await createBarcodeQrDetector(fakeBarcodeDetector({ codes: [] }));
    const empty = await createBarcodeQrDetector(fakeBarcodeDetector({ codes: [{ rawValue: '' }] }));

    expect(await none?.detect(FRAME)).toBeUndefined();
    expect(await empty?.detect(FRAME)).toBeUndefined();
  });
});
