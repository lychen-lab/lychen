// @vitest-environment jsdom
import { useKeycloak, vueKeycloak } from '@josempgon/vue-keycloak';
import { createApp } from 'vue';
import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
import { getAccessToken } from './getAccessToken';

// The real vue-keycloak and keycloak-js run against a fake Keycloak: only its token endpoint
// (fetch) is faked. A sign-in hands keycloak-js a refresh token, which it exchanges at once.

const config = { url: 'https://keycloak.example.org', realm: 'lychen', clientId: 'app' };
const TOKEN_URL = `${config.url}/realms/lychen/protocol/openid-connect/token`;

let tokenEndpoint: Array<() => Promise<Response>>;
const fetchMock = vi.fn(async (url: string) => {
  const respond = tokenEndpoint.shift();
  if (url !== TOKEN_URL || !respond) {
    throw new Error(`Unexpected request to ${url}.`);
  }
  return respond();
});

function base64Url(part: object): string {
  return Buffer.from(JSON.stringify(part)).toString('base64url');
}

/** An unsigned JWT: keycloak-js only reads the payload. */
function jwt(lifetime: number): string {
  const now = Math.floor(Date.now() / 1000);
  const payload = { sub: 'user-1', iat: now, exp: now + lifetime, jti: crypto.randomUUID() };
  return `${base64Url({ alg: 'none' })}.${base64Url(payload)}.`;
}

/** The token endpoint issues this access token, and a refresh token. */
function issues(accessToken: string): () => Promise<Response> {
  return async () => Response.json({ access_token: accessToken, refresh_token: jwt(1800) });
}

function answers(status: number): () => Promise<Response> {
  return async () => Response.json({ error: 'invalid_grant' }, { status });
}

/** Installs the plugin, as an app's main.ts does, signed in when `signedIn`. */
function install(signedIn: boolean): Promise<void> {
  // No onLoad: keycloak-js would redirect to the login page, which jsdom can't follow.
  const initOptions = signedIn
    ? { onLoad: undefined, token: jwt(300), refreshToken: jwt(1800) }
    : { onLoad: undefined };
  return vueKeycloak.install(createApp({}), { config, initOptions });
}

beforeEach(() => {
  tokenEndpoint = [];
  fetchMock.mockClear();
  vi.stubGlobal('fetch', fetchMock);
  // vue-keycloak logs every failure.
  vi.spyOn(console, 'error').mockImplementation(() => undefined);
});

afterEach(() => {
  vi.unstubAllGlobals();
  vi.restoreAllMocks();
});

describe('getAccessToken', () => {
  it('gives no token when nobody is signed in', async () => {
    await install(false);

    await expect(getAccessToken()).resolves.toBeUndefined();
    expect(fetchMock).not.toHaveBeenCalled();
  });

  it("gives the signed-in user's token", async () => {
    const token = jwt(300);
    tokenEndpoint.push(issues(token));
    await install(true);

    await expect(getAccessToken()).resolves.toBe(token);
    expect(fetchMock).toHaveBeenCalledTimes(1);
  });

  it('refreshes a token that is about to expire', async () => {
    const fresh = jwt(300);
    tokenEndpoint.push(issues(jwt(5)), issues(fresh));
    await install(true);

    await expect(getAccessToken()).resolves.toBe(fresh);
    expect(fetchMock).toHaveBeenCalledTimes(2);
  });

  it('waits for the sign-in, which app.use() does not', async () => {
    const token = jwt(300);
    let signIn!: () => void;
    tokenEndpoint.push(() =>
      new Promise<void>((resolve) => (signIn = resolve)).then(issues(token)),
    );
    void install(true);
    const accessToken = getAccessToken();

    // Keycloak answers only once the token was asked for.
    await vi.waitFor(() => expect(fetchMock).toHaveBeenCalled());
    signIn();

    await expect(accessToken).resolves.toBe(token);
  });

  it('gives no token once Keycloak refuses the refresh token', async () => {
    tokenEndpoint.push(issues(jwt(5)), answers(400));
    await install(true);

    await expect(getAccessToken()).resolves.toBeUndefined();
    expect(useKeycloak().isAuthenticated.value).toBe(false);
  });

  it('fails when Keycloak cannot be reached', async () => {
    tokenEndpoint.push(issues(jwt(5)), answers(503));
    await install(true);

    await expect(getAccessToken()).rejects.toThrow();
    expect(useKeycloak().isAuthenticated.value).toBe(true);
  });
});
