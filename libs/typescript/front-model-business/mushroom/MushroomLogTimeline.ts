import { type ObjectValues } from '@lychen/typescript-utils/transformers/ObjectValues';
import type { Harvest } from './Harvest';
import type { Inoculation } from './Inoculation';
import type { JournalEntry } from './JournalEntry';

/** What a line of a log's timeline is: the three things that happen to a log. */
export const MUSHROOM_LOG_TIMELINE_ITEM_TYPE = {
  Inoculation: 'inoculation',
  /** Any kind of journal entry, the API's `state_change` ones included (see its `kind`). */
  JournalEntry: 'journal_entry',
  Harvest: 'harvest',
} as const;

export type MushroomLogTimelineItemType = ObjectValues<typeof MUSHROOM_LOG_TIMELINE_ITEM_TYPE>;

interface TimelineItemBase {
  /** Unique within a timeline, to key a list on: the type and the item's id. */
  key: string;
  /** When it happened. Undefined only for an item the API gave without a date. */
  date?: Date;
}

/**
 * A line of a log's timeline (spec: "chronologie : inoculation, journal, récoltes, changements
 * d'état, du plus récent au plus ancien"): the item itself, under a `type` to tell them apart.
 */
export type MushroomLogTimelineItem =
  | (TimelineItemBase & {
      type: typeof MUSHROOM_LOG_TIMELINE_ITEM_TYPE.Inoculation;
      inoculation: Inoculation;
    })
  | (TimelineItemBase & {
      type: typeof MUSHROOM_LOG_TIMELINE_ITEM_TYPE.JournalEntry;
      journalEntry: JournalEntry;
    })
  | (TimelineItemBase & {
      type: typeof MUSHROOM_LOG_TIMELINE_ITEM_TYPE.Harvest;
      harvest: Harvest;
    });

/** What a log's timeline is made of. */
export interface MushroomLogTimelineSources {
  /** One per log in v0. */
  inoculation?: Inoculation;
  journalEntries?: readonly JournalEntry[];
  harvests?: readonly Harvest[];
}

// Of two items at the very same instant, the one that comes first in the (most recent first) list:
// a journal entry (the state change a harvest or an inoculation causes) before its cause.
const RANK: Record<MushroomLogTimelineItemType, number> = {
  [MUSHROOM_LOG_TIMELINE_ITEM_TYPE.JournalEntry]: 0,
  [MUSHROOM_LOG_TIMELINE_ITEM_TYPE.Harvest]: 1,
  [MUSHROOM_LOG_TIMELINE_ITEM_TYPE.Inoculation]: 2,
};

/**
 * Merges a log's inoculation, journal entries and harvests into one list, from the most recent
 * to the oldest. An inoculation and a harvest are dated to the day, a journal entry to the
 * moment: two items of the same instant keep a fixed order (journal, harvest, inoculation), and
 * two of the same type and instant keep the order they came in. An item without a date goes last
 * rather than vanishing. The sources are not modified.
 */
export function buildMushroomLogTimeline(
  sources: MushroomLogTimelineSources,
): MushroomLogTimelineItem[] {
  const items: MushroomLogTimelineItem[] = [];

  if (sources.inoculation) {
    items.push({
      type: MUSHROOM_LOG_TIMELINE_ITEM_TYPE.Inoculation,
      key: itemKey(MUSHROOM_LOG_TIMELINE_ITEM_TYPE.Inoculation, sources.inoculation.id, 0),
      date: sources.inoculation.inoculatedAt,
      inoculation: sources.inoculation,
    });
  }

  (sources.journalEntries ?? []).forEach((journalEntry, index) => {
    items.push({
      type: MUSHROOM_LOG_TIMELINE_ITEM_TYPE.JournalEntry,
      key: itemKey(MUSHROOM_LOG_TIMELINE_ITEM_TYPE.JournalEntry, journalEntry.id, index),
      date: journalEntry.occurredAt,
      journalEntry,
    });
  });

  (sources.harvests ?? []).forEach((harvest, index) => {
    items.push({
      type: MUSHROOM_LOG_TIMELINE_ITEM_TYPE.Harvest,
      key: itemKey(MUSHROOM_LOG_TIMELINE_ITEM_TYPE.Harvest, harvest.id, index),
      date: harvest.harvestedAt,
      harvest,
    });
  });

  return items.sort(compareMostRecentFirst);
}

function itemKey(type: MushroomLogTimelineItemType, id: string | undefined, index: number): string {
  return `${type}:${id ?? `#${index}`}`;
}

function compareMostRecentFirst(a: MushroomLogTimelineItem, b: MushroomLogTimelineItem): number {
  const aTime = a.date?.getTime();
  const bTime = b.date?.getTime();

  if (aTime === undefined || bTime === undefined) {
    // Undated items go last; two of them keep their order.
    return aTime === bTime ? 0 : aTime === undefined ? 1 : -1;
  }

  return bTime - aTime || RANK[a.type] - RANK[b.type];
}
