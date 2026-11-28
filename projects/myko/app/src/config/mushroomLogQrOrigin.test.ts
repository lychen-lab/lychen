import { afterEach, describe, expect, it, vi } from 'vitest';

import { getMushroomLogQrOrigin } from './mushroomLogQrOrigin';

describe('getMushroomLogQrOrigin', () => {
  afterEach(() => {
    vi.unstubAllEnvs();
    vi.unstubAllGlobals();
  });

  it('is the configured app URL', () => {
    vi.stubEnv('VITE_MYKO_APP_URL', 'https://myko.lychen.org/');
    vi.stubGlobal('window', { location: { origin: 'https://localhost:5820' } });

    expect(getMushroomLogQrOrigin()).toBe('https://myko.lychen.org');
  });

  it.each([[''], ['${MYKO_APP_URL}']])(
    'is the origin the app runs on when nothing usable is configured (%j)',
    (value) => {
      vi.stubEnv('VITE_MYKO_APP_URL', value);
      vi.stubGlobal('window', { location: { origin: 'https://localhost:5820' } });

      expect(getMushroomLogQrOrigin()).toBe('https://localhost:5820');
    },
  );
});
