import { afterEach, describe, expect, it, vi } from 'vitest';
import { effectScope, nextTick, ref } from 'vue';

import { formatDocumentTitle, useDocumentTitle } from './useDocumentTitle';

describe('formatDocumentTitle', () => {
  it('puts the screen first and the app last', () => {
    expect(formatDocumentTitle('Bûches')).toBe('Bûches · Myko');
    expect(formatDocumentTitle('  B-012 ')).toBe('B-012 · Myko');
  });

  it.each([[undefined], [null], [''], ['   '], ['Myko']])(
    'is the name of the app alone for %j',
    (title) => {
      expect(formatDocumentTitle(title)).toBe('Myko');
    },
  );
});

describe('useDocumentTitle', () => {
  afterEach(() => {
    vi.unstubAllGlobals();
  });

  it('titles the document at once, and again when the title changes', async () => {
    const document = { title: 'Myko' };
    vi.stubGlobal('document', document);
    const screenTitle = ref<string | undefined>('Zones');

    const scope = effectScope();
    scope.run(() => useDocumentTitle(screenTitle));

    expect(document.title).toBe('Zones · Myko');

    screenTitle.value = 'Nouvelle zone';
    await nextTick();
    expect(document.title).toBe('Nouvelle zone · Myko');

    screenTitle.value = undefined;
    await nextTick();
    expect(document.title).toBe('Myko');

    scope.stop();
  });

  it('follows a getter', async () => {
    const document = { title: '' };
    vi.stubGlobal('document', document);
    const label = ref('B-001');

    const scope = effectScope();
    scope.run(() => useDocumentTitle(() => label.value));
    expect(document.title).toBe('B-001 · Myko');

    label.value = 'B-002';
    await nextTick();
    expect(document.title).toBe('B-002 · Myko');

    scope.stop();
  });

  it('stops following once its scope is gone', async () => {
    const document = { title: '' };
    vi.stubGlobal('document', document);
    const screenTitle = ref('Souches');

    const scope = effectScope();
    scope.run(() => useDocumentTitle(screenTitle));
    scope.stop();

    screenTitle.value = 'Zones';
    await nextTick();
    expect(document.title).toBe('Souches · Myko');
  });

  it('does nothing where there is no document', () => {
    vi.stubGlobal('document', undefined);

    const scope = effectScope();

    expect(() => scope.run(() => useDocumentTitle('Zones'))).not.toThrow();
    scope.stop();
  });
});
