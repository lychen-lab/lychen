import { describe, expect, it, vi } from 'vitest';

import { createQrDetector } from './createQrDetector';
import type { QrDetector } from './qrDetector';

const native: QrDetector = { detect: async () => 'native' };
const fallback: QrDetector = { detect: async () => 'fallback' };

describe('createQrDetector', () => {
  it("takes the browser's own detector, and does not even load the library", async () => {
    const createFallback = vi.fn(async () => fallback);

    const detector = await createQrDetector({ createNative: async () => native, createFallback });

    expect(detector).toBe(native);
    expect(createFallback).not.toHaveBeenCalled();
  });

  it('takes the library where the browser has no detector', async () => {
    const detector = await createQrDetector({
      createNative: async () => undefined,
      createFallback: async () => fallback,
    });

    expect(detector).toBe(fallback);
  });

  it('has no detector when neither can be made', async () => {
    const detector = await createQrDetector({
      createNative: async () => undefined,
      createFallback: async () => undefined,
    });

    expect(detector).toBeUndefined();
  });
});
