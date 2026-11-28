import { describe, expect, it } from 'vitest';

import { mushroomLogQrContent } from '../qr/mushroomLogQr';
import { resolveScannedContent, SCAN_RESULT_TYPE } from './scanResult';

const UUID = '0190a3c4-5b6d-7e8f-9a0b-1c2d3e4f5a6b';

describe('resolveScannedContent', () => {
  it('opens the log of a label', () => {
    expect(resolveScannedContent(`https://myko.example/l/${UUID}`)).toEqual({
      type: SCAN_RESULT_TYPE.MushroomLog,
      uuid: UUID,
    });
  });

  it('reads what the QR code of a log holds, whatever the origin', () => {
    for (const origin of [
      'https://myko.example',
      'http://localhost:5820',
      'https://staging.example',
    ]) {
      expect(resolveScannedContent(mushroomLogQrContent(origin, UUID))).toEqual({
        type: SCAN_RESULT_TYPE.MushroomLog,
        uuid: UUID,
      });
    }
  });

  it('gives the uuid in lower case and ignores the spaces around the content', () => {
    expect(resolveScannedContent(`  https://myko.example/l/${UUID.toUpperCase()}\n`)).toEqual({
      type: SCAN_RESULT_TYPE.MushroomLog,
      uuid: UUID,
    });
  });

  it.each([
    ['another website', 'https://example.com/products/42'],
    ['plain text', 'B-012'],
    ['a label path with something else than a uuid', 'https://myko.example/l/not-a-uuid'],
    ['a label path without a uuid', 'https://myko.example/l/'],
    ['a javascript address', `javascript:alert('${UUID}')`],
    ['an empty content', ''],
  ])('says it is not a Myko label for %s, with the content as it was read', (_name, content) => {
    expect(resolveScannedContent(content)).toEqual({ type: SCAN_RESULT_TYPE.NotMyko, content });
  });
});
