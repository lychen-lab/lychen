import { describe, expect, it } from 'vitest';

import { MUSHROOM_LOG_STATE } from './MushroomLog';
import {
  BULK_INOCULATION_SKIP_REASON,
  splitLogsForInoculation,
} from './MushroomLogBulkInoculation';

describe('splitLogsForInoculation', () => {
  it('keeps the prepared logs and reports the others, in the order of the selection', () => {
    const logs = [
      { id: '1', label: 'B-001', state: MUSHROOM_LOG_STATE.Prepared },
      { id: '2', label: 'B-002', state: MUSHROOM_LOG_STATE.Incubating },
      { id: '3', label: 'B-003', state: MUSHROOM_LOG_STATE.Prepared },
      { id: '4', label: 'B-004', state: MUSHROOM_LOG_STATE.Fruiting },
      { id: '5', label: 'B-005', state: MUSHROOM_LOG_STATE.Retired },
    ];

    const { eligible, skipped } = splitLogsForInoculation(logs);

    expect(eligible).toEqual([logs[0], logs[2]]);
    expect(skipped).toEqual([
      { log: logs[1], reason: BULK_INOCULATION_SKIP_REASON.NotPrepared },
      { log: logs[3], reason: BULK_INOCULATION_SKIP_REASON.NotPrepared },
      { log: logs[4], reason: BULK_INOCULATION_SKIP_REASON.NotPrepared },
    ]);
  });

  it('does not assume a log of an unknown state is prepared', () => {
    const log = { id: '1', state: undefined };

    expect(splitLogsForInoculation([log])).toEqual({
      eligible: [],
      skipped: [{ log, reason: BULK_INOCULATION_SKIP_REASON.NotPrepared }],
    });
  });

  it('gives nothing for an empty selection', () => {
    expect(splitLogsForInoculation([])).toEqual({ eligible: [], skipped: [] });
  });

  it('keeps the type of the logs it is given', () => {
    const logs = [{ id: 'a', state: MUSHROOM_LOG_STATE.Prepared, label: 'B-001' }];

    expect(splitLogsForInoculation(logs).eligible[0]?.label).toBe('B-001');
  });
});
