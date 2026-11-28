import { describe, expect, it } from 'vitest';

import { createJsQrDetector } from '../scan/jsQrDetector';
import { mushroomLogQrContent } from '../qr/mushroomLogQr';
import { mushroomLogQrCode } from '../qr/mushroomLogQrCode';
import { LABEL_SHEET_SIZE, type LabelSheetSize, labelSheetLayout } from './labelSheetLayout';

const CONTENT = mushroomLogQrContent(
  'https://myko.example',
  '0198a2b4-7c1e-7d3a-9f10-5b6c7d8e9f01',
);

/** Dark bark around the sticker, in grey levels: the worst ground for a code's quiet zone. */
const BARK = 40;
const INK = 0;
const PAPER = 255;
const BARK_MARGIN_MM = 6;

/**
 * What a camera sees of a printed label stuck on a log: the label of `labelSheetLayout` (white
 * paper, its QR code at the place and size the layout gives, a solid dark block where the text
 * goes, which is worse than any real text) on dark bark, at `dpi` dots per inch. A module's edges
 * are rounded to the nearest dot, as a printer does, so the code is as irregular as a real one.
 */
function photographLabel(size: LabelSheetSize, dpi: number) {
  const layout = labelSheetLayout(size);
  const dotsPerMm = dpi / 25.4;
  function dots(mm: number): number {
    return Math.round(mm * dotsPerMm);
  }

  const width = dots(layout.label.widthMm + 2 * BARK_MARGIN_MM);
  const height = dots(layout.label.heightMm + 2 * BARK_MARGIN_MM);
  const grey = new Uint8Array(width * height).fill(BARK);

  function fill(x0: number, y0: number, x1: number, y1: number, value: number): void {
    for (let y = y0; y < y1; y++) {
      grey.fill(value, y * width + x0, y * width + x1);
    }
  }

  const labelX = BARK_MARGIN_MM;
  const labelY = BARK_MARGIN_MM;
  fill(
    dots(labelX),
    dots(labelY),
    dots(labelX + layout.label.widthMm),
    dots(labelY + layout.label.heightMm),
    PAPER,
  );

  const { size: modules, path } = mushroomLogQrCode(CONTENT);
  const moduleMm = layout.qr.sideMm / modules;
  const qrX = labelX + layout.qr.xMm;
  const qrY = labelY + layout.qr.yMm;
  for (const [, x, y, length] of path.matchAll(/M(\d+) (\d+)h(\d+)v1h-\d+z/g)) {
    fill(
      dots(qrX + Number(x) * moduleMm),
      dots(qrY + Number(y) * moduleMm),
      dots(qrX + (Number(x) + Number(length)) * moduleMm),
      dots(qrY + (Number(y) + 1) * moduleMm),
      INK,
    );
  }

  fill(
    dots(labelX + layout.text.xMm),
    dots(labelY + layout.text.yMm + layout.text.heightMm * 0.3),
    dots(labelX + layout.text.xMm + layout.text.widthMm),
    dots(labelY + layout.text.yMm + layout.text.heightMm * 0.7),
    INK,
  );

  const pixels = new Uint8ClampedArray(width * height * 4);
  grey.forEach((value, index) => pixels.set([value, value, value, 255], index * 4));

  return { pixels, width, height, dotsPerModule: dots(moduleMm * 10) / 10 };
}

async function readLabel(size: LabelSheetSize, dpi: number): Promise<string | undefined> {
  const picture = photographLabel(size, dpi);
  const detector = await createJsQrDetector({
    createFrameCanvas: () => ({ read: () => picture }),
  });

  return detector?.detect({} as HTMLVideoElement);
}

// The sizes of `labelSheetLayout` are chosen for the sticker sheets people buy, and the QR code is
// as large as the label's height leaves it. A real decoder (jsQR) must still read both, with the
// quiet zone the layout leaves (the label's padding) and a dark block next to the code, stuck on
// dark bark. The picture is sharp and noiseless, so this guards the layout (sizes, padding, where
// the text goes), not a camera: 100 dpi is 2 dots a module on the compact label, where the
// decoder still has room (it reads down to about 80 dpi).
describe.each([LABEL_SHEET_SIZE.Standard, LABEL_SHEET_SIZE.Compact])(
  'a %s label stuck on a log',
  (size) => {
    it.each([300, 200, 100])('is read from a %i dpi picture', async (dpi) => {
      expect(await readLabel(size, dpi)).toBe(CONTENT);
    });
  },
);
