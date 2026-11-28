/** How many items of a bulk entry are in flight at once, unless the caller says otherwise. */
export const BULK_DEFAULT_CONCURRENCY = 3;

export const BULK_ITEM_STATUS = {
  Succeeded: 'succeeded',
  Failed: 'failed',
} as const;

export type BulkItemStatus = (typeof BULK_ITEM_STATUS)[keyof typeof BULK_ITEM_STATUS];

/** What happened to one item: `error` is whatever its task threw, for the screen to translate. */
export type BulkItemResult<Item> =
  | { item: Item; status: typeof BULK_ITEM_STATUS.Succeeded }
  | { item: Item; status: typeof BULK_ITEM_STATUS.Failed; error: unknown };

/** Where a bulk entry stands: what the progress bar and, at the end, the recap say. */
export interface BulkProgress {
  total: number;
  /** Items whose task is over, succeeded or failed. */
  done: number;
  succeeded: number;
  failed: number;
}

export interface RunBulkOptions {
  /** At least 1: a smaller or non-integer value is rounded up to 1 or down to a whole number. */
  concurrency?: number;
  /** Called after each item is over, with the progress so far, and once with 0 done at the start. */
  onProgress?: (progress: BulkProgress) => void;
}

/** The progress of a bulk entry of `total` items, given the results so far. */
export function bulkProgress(
  total: number,
  results: readonly { status: BulkItemStatus }[],
): BulkProgress {
  const succeeded = results.filter((result) => result.status === BULK_ITEM_STATUS.Succeeded).length;
  const failed = results.length - succeeded;

  return { total, done: results.length, succeeded, failed };
}

/**
 * Runs `task` on every item (the same journal entry on every selected log, the same inoculation on
 * every prepared one), a few at a time, and says what happened to each. It never rejects: a task that
 * rejects or throws makes its item `failed` and the others carry on, so that nine logs are saved even
 * when the tenth is refused. The results come back in the order of `items`, whatever the order the
 * tasks finished in.
 */
export async function runBulk<Item>(
  items: readonly Item[],
  task: (item: Item) => Promise<unknown> | unknown,
  options: RunBulkOptions = {},
): Promise<BulkItemResult<Item>[]> {
  const concurrency = Math.max(1, Math.floor(options.concurrency ?? BULK_DEFAULT_CONCURRENCY) || 1);
  const results = new Array<BulkItemResult<Item>>(items.length);
  const finished: { status: BulkItemStatus }[] = [];
  let next = 0;

  options.onProgress?.(bulkProgress(items.length, finished));

  async function worker(): Promise<void> {
    while (next < items.length) {
      const index = next;
      next += 1;
      const item = items[index] as Item;

      try {
        await task(item);
        results[index] = { item, status: BULK_ITEM_STATUS.Succeeded };
      } catch (error) {
        results[index] = { item, status: BULK_ITEM_STATUS.Failed, error };
      }

      finished.push(results[index] as BulkItemResult<Item>);
      options.onProgress?.(bulkProgress(items.length, finished));
    }
  }

  await Promise.all(Array.from({ length: Math.min(concurrency, items.length) }, () => worker()));

  return results;
}
