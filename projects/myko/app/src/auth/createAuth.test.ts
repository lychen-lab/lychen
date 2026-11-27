import { afterEach, describe, expect, it, vi } from 'vitest';

import type { Auth } from './Auth';

const zitadelAuth = { isConfigured: false } as Auth;
const createZitadelAuth = vi.fn(() => zitadelAuth);

// Zitadel itself is not under test, and its library needs a browser.
vi.mock('./zitadel/createZitadelAuth', () => ({ createZitadelAuth }));

describe('createAuth', () => {
  afterEach(() => {
    vi.unstubAllEnvs();
    vi.clearAllMocks();
  });

  it('uses Zitadel by default', async () => {
    const { createAuth } = await import('./createAuth');

    expect(await createAuth()).toBe(zitadelAuth);
    expect(createZitadelAuth).toHaveBeenCalledOnce();
  });

  it('uses Zitadel when the fake login is turned off', async () => {
    vi.stubEnv('VITE_MYKO_FAKE_AUTH', 'false');
    const { createAuth } = await import('./createAuth');

    expect(await createAuth()).toBe(zitadelAuth);
  });

  it('uses the fake login only in development, when VITE_MYKO_FAKE_AUTH is "true"', async () => {
    vi.stubEnv('VITE_MYKO_FAKE_AUTH', 'true');
    const { createAuth } = await import('./createAuth');

    // Vitest runs with import.meta.env.DEV on, like `vite dev`.
    const auth = await createAuth();
    expect(auth).not.toBe(zitadelAuth);
    expect(auth.isConfigured).toBe(true);
    expect(createZitadelAuth).not.toHaveBeenCalled();
  });

  it('never uses the fake login in production, whatever the variable says', async () => {
    vi.stubEnv('VITE_MYKO_FAKE_AUTH', 'true');
    vi.stubEnv('DEV', false);
    const { createAuth } = await import('./createAuth');

    expect(await createAuth()).toBe(zitadelAuth);
    expect(createZitadelAuth).toHaveBeenCalledOnce();
  });
});
