/**
 * The label a new log (or the first of a series) is suggested, from the labels the person already
 * uses: `B-001` to `B-040` suggest `B-041`.
 */
export interface MushroomLogLabelSuggestion {
  /** What comes before the number in the labels the person uses most (`B-`). */
  prefix: string;
  /** The number after the highest one used with that prefix: gaps are not filled. */
  startNumber: number;
  /** The digits that prefix's numbers are written with (`B-041`: 3), so that labels sort. */
  digits: number;
  /** The next label itself (`B-041`), for a single log. */
  label: string;
}

// A label ends with a number (`B-012`, `A1-007`): what comes before is its prefix.
const NUMBERED_LABEL = /^(.*?)(\d+)$/;

interface PrefixUsage {
  prefix: string;
  /** How many labels use the prefix. */
  count: number;
  /** The highest number used with it, and how many digits that one is written with. */
  highest: number;
  digits: number;
}

/**
 * The next label, from the labels the person already has: the prefix most of them share, then the
 * number after the highest one used with it, written with as many digits as that one. A tie between
 * two prefixes goes to the one that goes furthest, then to the first in alphabetical order, so that
 * the suggestion never changes with the order of the labels. Undefined while no label ends with a
 * number: the form starts on its own defaults then.
 *
 * Only a suggestion: that the label is free among the person's logs still only the API knows (a
 * label typed elsewhere may have taken it since).
 */
export function suggestMushroomLogLabel(labels: string[]): MushroomLogLabelSuggestion | undefined {
  const usages = new Map<string, PrefixUsage>();

  for (const label of labels) {
    const match = NUMBERED_LABEL.exec(label.trim());

    if (!match) {
      continue;
    }

    const [, prefix = '', written = ''] = match;
    const number = Number(written);

    // A number too large to add 1 to is no numbering, whatever its prefix.
    if (!Number.isSafeInteger(number + 1)) {
      continue;
    }

    const usage = usages.get(prefix);

    if (!usage) {
      usages.set(prefix, { prefix, count: 1, highest: number, digits: written.length });
    } else {
      usage.count += 1;
      if (number > usage.highest || (number === usage.highest && written.length > usage.digits)) {
        usage.highest = number;
        usage.digits = written.length;
      }
    }
  }

  const [best] = [...usages.values()].sort(
    (first, second) =>
      second.count - first.count ||
      second.highest - first.highest ||
      first.prefix.localeCompare(second.prefix),
  );

  if (!best) {
    return undefined;
  }

  const startNumber = best.highest + 1;

  return {
    prefix: best.prefix,
    startNumber,
    digits: best.digits,
    label: `${best.prefix}${String(startNumber).padStart(best.digits, '0')}`,
  };
}
