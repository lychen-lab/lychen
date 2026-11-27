import type { Auth } from './Auth';

/**
 * Zitadel, or the fake login when VITE_MYKO_FAKE_AUTH=true in development
 * (projects/myko/CLAUDE.md, "Never blocked"). import.meta.env.DEV is false in a production
 * build, so the bundler drops the fake branch, its import and the variable's name.
 */
export async function createAuth(): Promise<Auth> {
  if (import.meta.env.DEV && import.meta.env.VITE_MYKO_FAKE_AUTH === 'true') {
    const { createFakeAuth } = await import('./fake/createFakeAuth');
    return createFakeAuth();
  }

  const { createZitadelAuth } = await import('./zitadel/createZitadelAuth');
  return createZitadelAuth();
}
