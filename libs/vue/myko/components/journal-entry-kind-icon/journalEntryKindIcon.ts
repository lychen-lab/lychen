import {
  Bandage,
  Droplets,
  Ellipsis,
  Eye,
  NotebookPen,
  RefreshCw,
  ShowerHead,
  Truck,
} from '@lucide/vue';
import type { Component } from 'vue';

import {
  JOURNAL_ENTRY_KIND,
  type JournalEntryKind,
} from '@lychen/typescript-front-model-business/mushroom/JournalEntry';

// Myko's own icons for its own vocabulary: libs/vue/icons is shared and stays untouched.
const ICON_BY_KIND: Record<JournalEntryKind, Component> = {
  [JOURNAL_ENTRY_KIND.Note]: NotebookPen,
  [JOURNAL_ENTRY_KIND.Observation]: Eye,
  [JOURNAL_ENTRY_KIND.Soaking]: Droplets,
  [JOURNAL_ENTRY_KIND.Watering]: ShowerHead,
  [JOURNAL_ENTRY_KIND.Moving]: Truck,
  [JOURNAL_ENTRY_KIND.Treatment]: Bandage,
  [JOURNAL_ENTRY_KIND.Other]: Ellipsis,
  [JOURNAL_ENTRY_KIND.StateChange]: RefreshCw,
};

/** The icon of a journal entry's kind, for a log's timeline. */
export function journalEntryKindIcon(kind: JournalEntryKind): Component {
  return ICON_BY_KIND[kind];
}
