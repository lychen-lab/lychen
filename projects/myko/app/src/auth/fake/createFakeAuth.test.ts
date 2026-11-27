import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';

import { createFakeAuth } from './createFakeAuth';

const STORAGE_KEY = 'myko-fake-auth-user';

function memoryStorage(): Storage {
  const items = new Map<string, string>();
  return {
    get length() {
      return items.size;
    },
    clear: () => items.clear(),
    getItem: (key) => items.get(key) ?? null,
    key: (index) => [...items.keys()][index] ?? null,
    removeItem: (key) => void items.delete(key),
    setItem: (key, value) => void items.set(key, value),
  };
}

describe('createFakeAuth', () => {
  beforeEach(() => {
    vi.stubGlobal('localStorage', memoryStorage());
  });

  afterEach(() => {
    vi.unstubAllGlobals();
  });

  it('starts signed out, with no token', () => {
    const auth = createFakeAuth();

    expect(auth.isConfigured).toBe(true);
    expect(auth.isAuthenticated.value).toBe(false);
    expect(auth.userName.value).toBeUndefined();
    expect(auth.getAccessToken()).toBeUndefined();
  });

  it('signs a fake user in and out', async () => {
    const auth = createFakeAuth();

    await auth.signIn();
    expect(auth.isAuthenticated.value).toBe(true);
    expect(auth.userName.value).toBe('Dev Myko');
    expect(auth.getAccessToken()).toBeUndefined();

    await auth.signOut();
    expect(auth.isAuthenticated.value).toBe(false);
    expect(auth.userName.value).toBeUndefined();
  });

  it('restores the session after a reload, until it signs out', async () => {
    await createFakeAuth().signIn();

    const reloaded = createFakeAuth();
    expect(reloaded.isAuthenticated.value).toBe(true);
    expect(reloaded.userName.value).toBe('Dev Myko');

    await reloaded.signOut();
    expect(localStorage.getItem(STORAGE_KEY)).toBeNull();
    expect(createFakeAuth().isAuthenticated.value).toBe(false);
  });

  it('still works for the page when the storage is unavailable', async () => {
    vi.stubGlobal('localStorage', {
      getItem: () => {
        throw new Error('denied');
      },
      setItem: () => {
        throw new Error('denied');
      },
      removeItem: () => {
        throw new Error('denied');
      },
    });
    const auth = createFakeAuth();

    expect(auth.isAuthenticated.value).toBe(false);
    await auth.signIn();
    expect(auth.isAuthenticated.value).toBe(true);
    await auth.signOut();
    expect(auth.isAuthenticated.value).toBe(false);
  });
});
