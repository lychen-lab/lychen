import { encode } from 'uqr';
import { describe, expect, it } from 'vitest';

import { LABEL_SHEET_SIZE, labelSheetLayout } from '../labels/labelSheetLayout';
import { mushroomLogQrCode } from './mushroomLogQrCode';
import { mushroomLogQrContent } from './mushroomLogQr';

const UUID = '0192f3a4-7b1c-7d2e-8f3a-4b5c6d7e8f90';
const OTHER_UUID = '0192f3a4-7b1c-7d2e-8f3a-4b5c6d7e8f91';
const CONTENT = mushroomLogQrContent('https://myko.lychen.org', UUID);

/** Reads the dark modules back from a path, the way a renderer would draw them. */
function modulesOf(path: string, size: number): boolean[][] {
  const modules = Array.from({ length: size }, () => Array.from({ length: size }, () => false));

  for (const [, x, y, length] of path.matchAll(/M(\d+) (\d+)h(\d+)v1h-\d+z/g)) {
    for (let offset = 0; offset < Number(length); offset++) {
      modules[Number(y)]![Number(x) + offset] = true;
    }
  }

  return modules;
}

describe('mushroomLogQrCode', () => {
  it('draws exactly the modules of the QR code of the content', () => {
    const { size, path } = mushroomLogQrCode(CONTENT);

    expect(modulesOf(path, size)).toEqual(encode(CONTENT, { ecc: 'M', border: 0 }).data);
  });

  it('is a square of 17 + 4 × version modules, and the path is only rectangles', () => {
    const { size, path } = mushroomLogQrCode(CONTENT);

    expect((size - 17) % 4).toBe(0);
    expect(path).toMatch(/^(M\d+ \d+h\d+v1h-\d+z)+$/);
  });

  it('has the three finder squares: a dark ring of 7 modules in three corners', () => {
    const { size, path } = mushroomLogQrCode(CONTENT);
    const modules = modulesOf(path, size);

    for (const [left, top] of [
      [0, 0],
      [size - 7, 0],
      [0, size - 7],
    ] as const) {
      for (let i = 0; i < 7; i++) {
        expect(modules[top]![left + i]).toBe(true);
        expect(modules[top + 6]![left + i]).toBe(true);
        expect(modules[top + i]![left]).toBe(true);
        expect(modules[top + i]![left + 6]).toBe(true);
      }
      expect(modules[top + 1]![left + 1]).toBe(false);
    }
  });

  it('gives the same code for the same content, another one for another log', () => {
    expect(mushroomLogQrCode(CONTENT)).toEqual(mushroomLogQrCode(CONTENT));
    expect(mushroomLogQrCode(CONTENT).path).not.toBe(
      mushroomLogQrCode(mushroomLogQrContent('https://myko.lychen.org', OTHER_UUID)).path,
    );
  });

  it('stays small enough to print and scan on both label sizes', () => {
    const { size } = mushroomLogQrCode(CONTENT);

    // About 0.45 mm a module is the least a phone reads reliably from a sticker.
    for (const labelSize of Object.values(LABEL_SHEET_SIZE)) {
      expect(labelSheetLayout(labelSize).qr.sideMm / size).toBeGreaterThanOrEqual(0.45);
    }
  });
});
