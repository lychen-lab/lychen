import { describe, expect, it } from 'vitest';

import {
  MYKO_LOCALES,
  compareLocales,
  groupLocaleModules,
  type LocaleMessages,
} from '@lychen/typescript-myko/i18n/localeParity';

// Every locale folder of the library, found by itself: a new composable's translations are checked
// without anyone adding them here.
const FOLDERS = groupLocaleModules(
  import.meta.glob<LocaleMessages>('./composables/**/i18n/{fr-FR,en-US}.ts', {
    eager: true,
    import: 'default',
  }),
);

describe('the translations of vue-myko', () => {
  it('finds the locale files it checks', () => {
    expect(FOLDERS.length).toBeGreaterThan(0);
  });

  it.each(FOLDERS)('$folder: fr-FR and en-US say the same things', ({ messages }) => {
    const [first, second] = MYKO_LOCALES;
    const fr = messages[first];
    const en = messages[second];

    expect(fr, `${first} file`).toBeDefined();
    expect(en, `${second} file`).toBeDefined();
    expect(compareLocales(fr ?? {}, en ?? {})).toEqual([]);
  });
});
