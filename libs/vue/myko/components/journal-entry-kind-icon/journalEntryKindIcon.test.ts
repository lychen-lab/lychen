import { describe, expect, it } from 'vitest';

import { JOURNAL_ENTRY_KIND } from '@lychen/typescript-front-model-business/mushroom/JournalEntry';

import { journalEntryKindIcon } from './journalEntryKindIcon';

describe('journalEntryKindIcon', () => {
  it('gives every kind of the journal an icon', () => {
    for (const kind of Object.values(JOURNAL_ENTRY_KIND)) {
      expect(journalEntryKindIcon(kind)).toBeDefined();
    }
  });

  it('gives each kind its own icon, so that two kinds are never mistaken on a timeline', () => {
    const icons = Object.values(JOURNAL_ENTRY_KIND).map(journalEntryKindIcon);

    expect(new Set(icons).size).toBe(icons.length);
  });
});
