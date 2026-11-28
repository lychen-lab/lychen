import { type ObjectValues } from '@lychen/typescript-utils/transformers/ObjectValues';
import { MUSHROOM_LOG_STATE, type MushroomLog } from './MushroomLog';

/** Why a selected log is not inoculated by a bulk inoculation. */
export const BULK_INOCULATION_SKIP_REASON = {
  /** Only a `prepared` log can be inoculated; an unknown state is not assumed to be one. */
  NotPrepared: 'not-prepared',
} as const;

export type BulkInoculationSkipReason = ObjectValues<typeof BULK_INOCULATION_SKIP_REASON>;

export interface BulkInoculationSplit<Log> {
  /** The logs to inoculate, in the order of the selection. */
  eligible: Log[];
  /** The others, which the recap says were left out and why, in the order of the selection. */
  skipped: { log: Log; reason: BulkInoculationSkipReason }[];
}

/**
 * Splits the logs selected in the list for a bulk inoculation: the same inoculation goes on the
 * `prepared` ones, the others are ignored and reported (projects/myko/CLAUDE.md, "Log lifecycle":
 * `inoculate` goes from `prepared` to `incubating`, and a log has one inoculation in v0). The API still
 * has the last word: this only keeps the obvious refusals out of the run.
 */
export function splitLogsForInoculation<Log extends Pick<MushroomLog, 'state'>>(
  logs: readonly Log[],
): BulkInoculationSplit<Log> {
  const split: BulkInoculationSplit<Log> = { eligible: [], skipped: [] };

  for (const log of logs) {
    if (log.state === MUSHROOM_LOG_STATE.Prepared) {
      split.eligible.push(log);
    } else {
      split.skipped.push({ log, reason: BULK_INOCULATION_SKIP_REASON.NotPrepared });
    }
  }

  return split;
}
