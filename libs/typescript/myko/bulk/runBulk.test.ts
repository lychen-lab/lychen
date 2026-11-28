import { describe, expect, it, vi } from 'vitest';

import {
  BULK_DEFAULT_CONCURRENCY,
  BULK_ITEM_STATUS,
  bulkProgress,
  runBulk,
  type BulkProgress,
} from './runBulk';

/** A task that ends when the test says so, to control the order things finish in. */
function deferred() {
  let resolve!: () => void;
  const promise = new Promise<void>((done) => {
    resolve = done;
  });

  return { promise, resolve };
}

describe('runBulk', () => {
  it('runs the task on every item and gives the results in the order of the items', async () => {
    const first = deferred();
    const gates = [first, deferred(), deferred()];
    const finishedOrder: number[] = [];

    const promise = runBulk(
      [0, 1, 2],
      async (index) => {
        await gates[index]!.promise;
        finishedOrder.push(index);
      },
      { concurrency: 3 },
    );

    gates[2]!.resolve();
    gates[1]!.resolve();
    first.resolve();

    const results = await promise;

    expect(finishedOrder).toEqual([2, 1, 0]);
    expect(results).toEqual([
      { item: 0, status: BULK_ITEM_STATUS.Succeeded },
      { item: 1, status: BULK_ITEM_STATUS.Succeeded },
      { item: 2, status: BULK_ITEM_STATUS.Succeeded },
    ]);
  });

  it('never has more tasks in flight than the concurrency', async () => {
    expect(await maxInFlightWith({ concurrency: 4 }, 10)).toBe(4);
  });

  /** The highest number of tasks in flight at once while running `count` tasks. */
  async function maxInFlightWith(options: { concurrency?: number }, count = 8): Promise<number> {
    let inFlight = 0;
    let maxInFlight = 0;

    await runBulk(
      Array.from({ length: count }, (_, index) => index),
      async () => {
        inFlight += 1;
        maxInFlight = Math.max(maxInFlight, inFlight);
        await new Promise((resolve) => setTimeout(resolve, 1));
        inFlight -= 1;
      },
      options,
    );

    return maxInFlight;
  }

  it('runs a few tasks at a time by default', async () => {
    expect(await maxInFlightWith({})).toBe(BULK_DEFAULT_CONCURRENCY);
  });

  it.each([[0], [-3], [Number.NaN], [0.4]])(
    'runs one task at a time when the concurrency is %s',
    async (concurrency) => {
      expect(await maxInFlightWith({ concurrency })).toBe(1);
    },
  );

  it('marks a rejected or throwing task as failed, with its error, and carries on with the others', async () => {
    const refused = new Error('422');
    const crashed = new Error('boom');

    const results = await runBulk(['a', 'b', 'c', 'd'], (label) => {
      if (label === 'b') {
        return Promise.reject(refused);
      }
      if (label === 'c') {
        throw crashed;
      }

      return Promise.resolve();
    });

    expect(results).toEqual([
      { item: 'a', status: BULK_ITEM_STATUS.Succeeded },
      { item: 'b', status: BULK_ITEM_STATUS.Failed, error: refused },
      { item: 'c', status: BULK_ITEM_STATUS.Failed, error: crashed },
      { item: 'd', status: BULK_ITEM_STATUS.Succeeded },
    ]);
  });

  it('gives an empty result, and one progress report, for no items', async () => {
    const onProgress = vi.fn();

    expect(await runBulk([], () => undefined, { onProgress })).toEqual([]);
    expect(onProgress).toHaveBeenCalledExactlyOnceWith({
      total: 0,
      done: 0,
      succeeded: 0,
      failed: 0,
    });
  });

  it('reports the progress at the start and after each item', async () => {
    const reports: BulkProgress[] = [];

    await runBulk(
      [1, 2, 3],
      async (value) => {
        if (value === 2) {
          throw new Error('refused');
        }
      },
      { concurrency: 1, onProgress: (progress) => reports.push(progress) },
    );

    expect(reports).toEqual([
      { total: 3, done: 0, succeeded: 0, failed: 0 },
      { total: 3, done: 1, succeeded: 1, failed: 0 },
      { total: 3, done: 2, succeeded: 1, failed: 1 },
      { total: 3, done: 3, succeeded: 2, failed: 1 },
    ]);
  });
});

describe('bulkProgress', () => {
  it('counts what is over, and what succeeded or failed', () => {
    expect(
      bulkProgress(5, [
        { status: BULK_ITEM_STATUS.Succeeded },
        { status: BULK_ITEM_STATUS.Failed },
        { status: BULK_ITEM_STATUS.Succeeded },
      ]),
    ).toEqual({ total: 5, done: 3, succeeded: 2, failed: 1 });
  });
});
