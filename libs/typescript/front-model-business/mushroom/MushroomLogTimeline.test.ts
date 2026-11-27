import { describe, expect, it } from 'vitest';

import type { Harvest } from './Harvest';
import type { Inoculation } from './Inoculation';
import { JOURNAL_ENTRY_KIND, type JournalEntry } from './JournalEntry';
import { MUSHROOM_LOG_TIMELINE_ITEM_TYPE, buildMushroomLogTimeline } from './MushroomLogTimeline';

// Months are 0-based in Date: new Date(2026, 2, 12) is 12 March 2026, in local time.
const inoculation: Inoculation = { id: 'ino-1', inoculatedAt: new Date(2026, 2, 12) };
const soaking: JournalEntry = {
  id: 'j-1',
  kind: JOURNAL_ENTRY_KIND.Soaking,
  occurredAt: new Date(2026, 4, 2, 9, 30),
};
const stateChange: JournalEntry = {
  id: 'j-2',
  kind: JOURNAL_ENTRY_KIND.StateChange,
  occurredAt: new Date(2026, 2, 12, 18, 0),
  text: 'prepared → incubating',
};
const harvest: Harvest = { id: 'h-1', harvestedAt: new Date(2026, 4, 20), weightGrams: 850 };

function keys(items: { key: string }[]): string[] {
  return items.map((item) => item.key);
}

describe('buildMushroomLogTimeline', () => {
  it('merges the three sources, the most recent first', () => {
    const timeline = buildMushroomLogTimeline({
      inoculation,
      journalEntries: [stateChange, soaking],
      harvests: [harvest],
    });

    expect(keys(timeline)).toEqual([
      'harvest:h-1',
      'journal_entry:j-1',
      'journal_entry:j-2',
      'inoculation:ino-1',
    ]);
  });

  it('gives each item its type and its own object', () => {
    const [first, second, third] = buildMushroomLogTimeline({
      inoculation,
      journalEntries: [soaking],
      harvests: [harvest],
    });

    expect(first).toMatchObject({ type: MUSHROOM_LOG_TIMELINE_ITEM_TYPE.Harvest, harvest });
    expect(second).toMatchObject({
      type: MUSHROOM_LOG_TIMELINE_ITEM_TYPE.JournalEntry,
      journalEntry: soaking,
    });
    expect(third).toMatchObject({
      type: MUSHROOM_LOG_TIMELINE_ITEM_TYPE.Inoculation,
      inoculation,
    });
  });

  it('dates each item with its own date: the day of an inoculation, the moment of an entry', () => {
    const timeline = buildMushroomLogTimeline({
      inoculation,
      journalEntries: [soaking],
      harvests: [harvest],
    });

    expect(timeline.map((item) => item.date)).toEqual([
      harvest.harvestedAt,
      soaking.occurredAt,
      inoculation.inoculatedAt,
    ]);
  });

  it('puts the entry of the day of an inoculation before the inoculation, since it is later', () => {
    const timeline = buildMushroomLogTimeline({ inoculation, journalEntries: [stateChange] });

    expect(keys(timeline)).toEqual(['journal_entry:j-2', 'inoculation:ino-1']);
  });

  it('orders items of the very same instant: journal, then harvest, then inoculation', () => {
    const day = new Date(2026, 5, 1);
    const timeline = buildMushroomLogTimeline({
      inoculation: { id: 'i', inoculatedAt: day },
      journalEntries: [{ id: 'j', kind: JOURNAL_ENTRY_KIND.Note, occurredAt: day }],
      harvests: [{ id: 'h', harvestedAt: day }],
    });

    expect(keys(timeline)).toEqual(['journal_entry:j', 'harvest:h', 'inoculation:i']);
  });

  it('keeps the order of two items of the same type and instant', () => {
    const day = new Date(2026, 5, 1);
    const timeline = buildMushroomLogTimeline({
      harvests: [
        { id: 'a', harvestedAt: day },
        { id: 'b', harvestedAt: day },
        { id: 'c', harvestedAt: day },
      ],
    });

    expect(keys(timeline)).toEqual(['harvest:a', 'harvest:b', 'harvest:c']);
  });

  it('keeps an item without a date, last, instead of dropping it', () => {
    const timeline = buildMushroomLogTimeline({
      journalEntries: [{ id: 'no-date', kind: JOURNAL_ENTRY_KIND.Note }, soaking],
      harvests: [{ id: 'also-none' }, harvest],
    });

    expect(keys(timeline)).toEqual([
      'harvest:h-1',
      'journal_entry:j-1',
      'journal_entry:no-date',
      'harvest:also-none',
    ]);
  });

  it('keys an item without an id by its place in its source, so that keys stay unique', () => {
    const timeline = buildMushroomLogTimeline({
      journalEntries: [
        { kind: JOURNAL_ENTRY_KIND.Note, occurredAt: new Date(2026, 5, 2) },
        { kind: JOURNAL_ENTRY_KIND.Note, occurredAt: new Date(2026, 5, 1) },
      ],
    });

    expect(keys(timeline)).toEqual(['journal_entry:#0', 'journal_entry:#1']);
    expect(new Set(keys(timeline)).size).toBe(2);
  });

  it('is empty for a log with nothing yet, and for sources left out', () => {
    expect(buildMushroomLogTimeline({})).toEqual([]);
    expect(buildMushroomLogTimeline({ journalEntries: [], harvests: [] })).toEqual([]);
  });

  it('handles a log without an inoculation, as a prepared one has none', () => {
    const timeline = buildMushroomLogTimeline({ journalEntries: [soaking] });

    expect(keys(timeline)).toEqual(['journal_entry:j-1']);
  });

  it('does not modify its sources', () => {
    const journalEntries = [stateChange, soaking];
    const harvests = [harvest];

    buildMushroomLogTimeline({ inoculation, journalEntries, harvests });

    expect(journalEntries).toEqual([stateChange, soaking]);
    expect(harvests).toEqual([harvest]);
  });
});
