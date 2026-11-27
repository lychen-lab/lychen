import type { MushroomLogBatchInput } from './MushroomLogBatchInput';

/**
 * The most logs one batch creates. A product choice (the spec's example is 40): it keeps the
 * preview and the creation, which runs one request per log, from running away on a typo. Change it
 * here, nowhere else.
 */
export const MUSHROOM_LOG_BATCH_MAX_COUNT = 200;

/** The most digits a batch's numbers are padded to (`B-000012`): more is a typo. */
export const MUSHROOM_LOG_BATCH_MAX_DIGITS = 6;

/** The part of a batch that makes its labels. */
export type MushroomLogBatchNumbering = Pick<
  MushroomLogBatchInput,
  'prefix' | 'startNumber' | 'count' | 'digits'
>;

/**
 * Whether a batch's numbering makes labels: whole numbers, a start from 0, at least one log and
 * at most `MUSHROOM_LOG_BATCH_MAX_COUNT`, between 1 and `MUSHROOM_LOG_BATCH_MAX_DIGITS` digits. The
 * prefix is free (it may be empty).
 */
export function isValidMushroomLogBatchNumbering(numbering: MushroomLogBatchNumbering): boolean {
  const { startNumber, count, digits } = numbering;

  return (
    Number.isSafeInteger(startNumber) &&
    startNumber >= 0 &&
    Number.isSafeInteger(count) &&
    count >= 1 &&
    count <= MUSHROOM_LOG_BATCH_MAX_COUNT &&
    Number.isSafeInteger(digits) &&
    digits >= 1 &&
    digits <= MUSHROOM_LOG_BATCH_MAX_DIGITS
  );
}

/**
 * The labels a batch creates, in order: the prefix, then each number from `startNumber`, `count` of
 * them, padded with zeros to `digits` digits (`B-`, 1, 40, 3 gives `B-001` to `B-040`). A number
 * longer than `digits` is never cut (`B-`, 998, 4, 3 gives `B-998`, `B-999`, `B-1000`, `B-1001`).
 *
 * Empty for a numbering that is not valid (`isValidMushroomLogBatchNumbering`): a form shows no
 * preview until the person has typed one.
 */
export function mushroomLogBatchLabels(numbering: MushroomLogBatchNumbering): string[] {
  if (!isValidMushroomLogBatchNumbering(numbering)) {
    return [];
  }

  const { prefix, startNumber, count, digits } = numbering;

  return Array.from(
    { length: count },
    (_, index) => `${prefix}${String(startNumber + index).padStart(digits, '0')}`,
  );
}
