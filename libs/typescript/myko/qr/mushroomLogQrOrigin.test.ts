import { describe, expect, it } from 'vitest';

import { mushroomLogQrContent } from './mushroomLogQr';
import { resolveMushroomLogQrOrigin } from './mushroomLogQrOrigin';

const CURRENT = 'https://localhost:5820';

describe('resolveMushroomLogQrOrigin', () => {
  it('is the configured origin, whatever the app runs on', () => {
    expect(resolveMushroomLogQrOrigin('https://myko.lychen.org', CURRENT)).toBe(
      'https://myko.lychen.org',
    );
    expect(resolveMushroomLogQrOrigin('http://192.168.1.20:5820', CURRENT)).toBe(
      'http://192.168.1.20:5820',
    );
  });

  it('keeps only the origin of what it is given: no path, query, fragment or trailing slash', () => {
    expect(resolveMushroomLogQrOrigin('https://myko.lychen.org/', CURRENT)).toBe(
      'https://myko.lychen.org',
    );
    expect(resolveMushroomLogQrOrigin(' https://myko.lychen.org/logs?x=1#top\n', CURRENT)).toBe(
      'https://myko.lychen.org',
    );
  });

  it.each([
    ['unset', undefined],
    ['empty', ''],
    ['blank', '   '],
    ['an unfilled placeholder left by the image entrypoint', '${MYKO_APP_URL}'],
    ['not an address', 'myko.lychen.org'],
    ['a path only', '/logs'],
    ['another scheme', 'javascript:alert(1)'],
    ['a file address', 'file:///etc/hosts'],
  ])('falls back to the origin the app runs on when the value is %s', (_what, value) => {
    expect(resolveMushroomLogQrOrigin(value, CURRENT)).toBe(CURRENT);
  });

  it('ignores a path of the fallback too', () => {
    expect(resolveMushroomLogQrOrigin(undefined, 'https://localhost:5820/logs/1')).toBe(CURRENT);
  });

  it('gives an origin the QR content can be built from, in every case', () => {
    const uuid = '0192f3a4-7b1c-7d2e-8f3a-4b5c6d7e8f90';

    expect(mushroomLogQrContent(resolveMushroomLogQrOrigin(undefined, CURRENT), uuid)).toBe(
      `${CURRENT}/l/${uuid}`,
    );
    expect(
      mushroomLogQrContent(resolveMushroomLogQrOrigin('https://myko.lychen.org/', CURRENT), uuid),
    ).toBe(`https://myko.lychen.org/l/${uuid}`);
  });
});
