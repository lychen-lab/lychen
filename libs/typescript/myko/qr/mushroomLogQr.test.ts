import { describe, expect, it } from 'vitest';

import { mushroomLogQrContent, mushroomLogUuidFromQrContent } from './mushroomLogQr';

const UUID = '0192f3a4-7b1c-7d2e-8f3a-4b5c6d7e8f90';

describe('mushroomLogQrContent', () => {
  // These strings are on printed labels. If one of these expectations has to change, the labels
  // already printed stop working: the change is wrong, not the test.
  it('is the app origin, /l/, then the uuid: the format of the printed labels', () => {
    expect(mushroomLogQrContent('https://myko.lychen.org', UUID)).toBe(
      `https://myko.lychen.org/l/${UUID}`,
    );
    expect(mushroomLogQrContent('http://localhost:5820', UUID)).toBe(
      `http://localhost:5820/l/${UUID}`,
    );
  });

  it('ignores a trailing slash or a path of the origin it is given', () => {
    expect(mushroomLogQrContent('https://myko.lychen.org/', UUID)).toBe(
      `https://myko.lychen.org/l/${UUID}`,
    );
    expect(mushroomLogQrContent('https://myko.lychen.org/logs/', UUID)).toBe(
      `https://myko.lychen.org/l/${UUID}`,
    );
  });
});

describe('mushroomLogUuidFromQrContent', () => {
  it('reads back what mushroomLogQrContent writes', () => {
    const content = mushroomLogQrContent('https://myko.lychen.org', UUID);

    expect(mushroomLogUuidFromQrContent(content)).toBe(UUID);
    expect(mushroomLogUuidFromQrContent(content, 'https://myko.lychen.org')).toBe(UUID);
  });

  it('reads a label as a scanner gives it: spaces around, a trailing slash, upper case', () => {
    expect(mushroomLogUuidFromQrContent(` https://myko.lychen.org/l/${UUID}/\n`)).toBe(UUID);
    expect(mushroomLogUuidFromQrContent(`https://myko.lychen.org/l/${UUID.toUpperCase()}`)).toBe(
      UUID,
    );
  });

  it('ignores a query or a fragment after the uuid', () => {
    expect(mushroomLogUuidFromQrContent(`https://myko.lychen.org/l/${UUID}?utm=x#top`)).toBe(UUID);
  });

  it('refuses a label of another origin when the app origin is given', () => {
    const staging = `https://staging.myko.lychen.org/l/${UUID}`;

    expect(mushroomLogUuidFromQrContent(staging, 'https://myko.lychen.org')).toBeUndefined();
    expect(mushroomLogUuidFromQrContent(staging)).toBe(UUID);
  });

  it.each([
    ['text', 'hello'],
    ['an empty string', ''],
    ['another scheme', `ftp://myko.lychen.org/l/${UUID}`],
    ['a javascript: address', `javascript:alert('/l/${UUID}')`],
    ['another path', `https://myko.lychen.org/logs/${UUID}`],
    ['the path alone', `/l/${UUID}`],
    ['no uuid', 'https://myko.lychen.org/l/'],
    ['something that is not a uuid', 'https://myko.lychen.org/l/B-012'],
    ['a longer path', `https://myko.lychen.org/l/${UUID}/edit`],
    ['a uuid with a wrong length', `https://myko.lychen.org/l/${UUID}0`],
  ])('is not a Myko label: %s', (_, content) => {
    expect(mushroomLogUuidFromQrContent(content)).toBeUndefined();
  });
});
