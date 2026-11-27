import { type ObjectValues } from '@lychen/typescript-utils/transformers/ObjectValues';
import type { WithRequired } from '../WithRequired';
import type { MushroomLog } from './MushroomLog';

/** What a journal entry is about. */
export const JOURNAL_ENTRY_KIND = {
  Note: 'note',
  Observation: 'observation',
  Soaking: 'soaking',
  Watering: 'watering',
  Moving: 'moving',
  Treatment: 'treatment',
  Other: 'other',
  /** Written by the API on every change of state of the log, never by a person. */
  StateChange: 'state_change',
} as const;

export type JournalEntryKind = ObjectValues<typeof JOURNAL_ENTRY_KIND>;

/**
 * A line of a mushroom log's journal. Every field is optional: an endpoint says which ones it
 * gives, a component which ones it needs (`WithRequired`).
 */
export interface JournalEntry {
  id?: string;
  mushroomLog?: MushroomLog;
  occurredAt?: Date;
  /** Undefined for a kind this front does not know yet. */
  kind?: JournalEntryKind;
  /** Required for a note and an observation, optional for the other kinds. */
  text?: string;
  createdAt?: Date;
}

/** What the journal form gives: any kind but the state change, which only the API writes. */
export interface JournalEntryInput {
  /** Only its `id` is sent. */
  mushroomLog: WithRequired<NonNullable<JournalEntry['mushroomLog']>, 'id'>;
  /** The API uses the current time when left out. */
  occurredAt?: JournalEntry['occurredAt'];
  kind: Exclude<NonNullable<JournalEntry['kind']>, typeof JOURNAL_ENTRY_KIND.StateChange>;
  text?: JournalEntry['text'];
}
