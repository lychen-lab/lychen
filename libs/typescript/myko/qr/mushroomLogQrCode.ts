import { encode } from 'uqr';

/**
 * Level M recovers up to 15% of a damaged code: a scratched or faded sticker still scans, and the
 * code of a 62 character address stays at 33 × 33 modules, which the smaller label (17 mm) prints
 * at about half a millimetre a module. Level Q would need 41 × 41, a module of 0.41 mm there.
 */
const ERROR_CORRECTION = 'M';

/** A QR code, ready to draw as one SVG path. */
export interface MushroomLogQrCode {
  /** Modules on a side: the code is a square of `size` × `size` unit modules. */
  size: number;
  /**
   * Every dark module, as `M<x> <y>h<n>v1h-<n>z` rectangles (one per run of dark modules in a row),
   * in a `0 0 size size` view box. There is no quiet zone: the label's padding is the one.
   */
  path: string;
}

/**
 * The QR code of a content (`mushroomLogQrContent` writes it, never build it by hand), as the
 * path of an SVG. Nothing is rendered as markup, so nothing in it can carry a script.
 */
export function mushroomLogQrCode(content: string): MushroomLogQrCode {
  const { data, size } = encode(content, { ecc: ERROR_CORRECTION, border: 0 });
  const rectangles: string[] = [];

  data.forEach((modules, y) => {
    let x = 0;
    while (x < modules.length) {
      if (!modules[x]) {
        x++;
        continue;
      }
      const start = x;
      while (x < modules.length && modules[x]) {
        x++;
      }
      const length = x - start;
      rectangles.push(`M${start} ${y}h${length}v1h-${length}z`);
    }
  });

  return { size, path: rectangles.join('') };
}
