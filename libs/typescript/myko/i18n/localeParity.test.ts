import { describe, expect, it } from 'vitest';

import {
  compareLocales,
  flattenLocaleMessages,
  groupLocaleModules,
  messagePlaceholders,
} from './localeParity';

describe('flattenLocaleMessages', () => {
  it('names a nested message by its whole path', () => {
    expect(flattenLocaleMessages({ title: 'Plus', state: { prepared: 'Préparée' } })).toEqual({
      title: 'Plus',
      'state.prepared': 'Préparée',
    });
  });
});

describe('messagePlaceholders', () => {
  it('lists each placeholder once, sorted', () => {
    expect(messagePlaceholders('{name} a pesé {grams} g, merci {name}')).toEqual(['grams', 'name']);
  });

  it('knows list placeholders, spaces inside the braces, and a message without any', () => {
    expect(messagePlaceholders('{0} et { 1 }')).toEqual(['0', '1']);
    expect(messagePlaceholders('Se déconnecter')).toEqual([]);
  });
});

describe('compareLocales', () => {
  it('finds nothing between two languages that say the same things', () => {
    expect(
      compareLocales(
        {
          title: 'Plus',
          signed_in_as: 'Connecté en tant que {name}',
          state: { prepared: 'Préparée' },
        },
        { title: 'More', signed_in_as: 'Signed in as {name}', state: { prepared: 'Prepared' } },
      ),
    ).toEqual([]);
  });

  it('reports a key only one language has, naming the language that lacks it', () => {
    expect(compareLocales({ a: 'A', b: 'B' }, { a: 'A', c: 'C' })).toEqual([
      'b: missing in en-US',
      'c: missing in fr-FR',
    ]);
  });

  it('reports a message left empty, whatever the language', () => {
    expect(compareLocales({ a: 'A', b: '  ' }, { a: '', b: 'B' })).toEqual([
      'b: empty in fr-FR',
      'a: empty in en-US',
    ]);
  });

  it('reports a placeholder that one language lost or renamed', () => {
    expect(
      compareLocales(
        { signed_in_as: 'Connecté en tant que {name}', weight: '{grams} g' },
        { signed_in_as: 'Signed in', weight: '{weight} g' },
      ),
    ).toEqual([
      'signed_in_as: placeholders differ (fr-FR: name, en-US: none)',
      'weight: placeholders differ (fr-FR: grams, en-US: weight)',
    ]);
  });

  it('names the languages it is given', () => {
    expect(compareLocales({ a: 'A' }, {}, ['fr-FR', 'en-GB'])).toEqual(['a: missing in en-GB']);
  });
});

describe('groupLocaleModules', () => {
  it('groups the language files of a folder, and sorts the folders', () => {
    const fr = { a: 'A' };
    const en = { a: 'B' };

    expect(
      groupLocaleModules({
        './use-z/i18n/fr-FR.ts': fr,
        './use-a/i18n/en-US.ts': en,
        './use-z/i18n/en-US.ts': en,
        './use-a/i18n/fr-FR.ts': fr,
      }),
    ).toEqual([
      { folder: './use-a/i18n', messages: { 'en-US': en, 'fr-FR': fr } },
      { folder: './use-z/i18n', messages: { 'fr-FR': fr, 'en-US': en } },
    ]);
  });

  it('keeps a folder with one language only, so that a test can say the other is missing', () => {
    const [folder] = groupLocaleModules({ './use-a/i18n/fr-FR.ts': { a: 'A' } });

    expect(Object.keys(folder?.messages ?? {})).toEqual(['fr-FR']);
  });
});
