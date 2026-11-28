import { describe, expect, it } from 'vitest';
import { effectScope, ref } from 'vue';

import { SELECTION_STATE } from '@lychen/typescript-myko/selection/itemSelection';

import { useItemSelection } from './useItemSelection';

function inScope<T>(run: () => T): T {
  return effectScope().run(run) as T;
}

describe('useItemSelection', () => {
  it('starts with nothing selected', () => {
    const selection = inScope(() => useItemSelection(['a', 'b']));

    expect(selection.selectedIds.value).toEqual([]);
    expect(selection.count.value).toBe(0);
    expect(selection.state.value).toBe(SELECTION_STATE.None);
    expect(selection.isSelected('a')).toBe(false);
  });

  it('selects and unselects one item at a time, and says them in the order of the list', () => {
    const selection = inScope(() => useItemSelection(['a', 'b', 'c']));

    selection.toggle('c');
    selection.toggle('a');

    expect(selection.selectedIds.value).toEqual(['a', 'c']);
    expect(selection.count.value).toBe(2);
    expect(selection.state.value).toBe(SELECTION_STATE.Some);
    expect(selection.isSelected('a')).toBe(true);
    expect(selection.isSelected('b')).toBe(false);

    selection.toggle('a');

    expect(selection.selectedIds.value).toEqual(['c']);
  });

  it('selects the whole list, then unselects it', () => {
    const selection = inScope(() => useItemSelection(['a', 'b', 'c']));

    selection.toggleAll();
    expect(selection.selectedIds.value).toEqual(['a', 'b', 'c']);
    expect(selection.state.value).toBe(SELECTION_STATE.All);

    selection.toggleAll();
    expect(selection.selectedIds.value).toEqual([]);
    expect(selection.state.value).toBe(SELECTION_STATE.None);
  });

  it('completes a partial selection instead of emptying it', () => {
    const selection = inScope(() => useItemSelection(['a', 'b', 'c']));

    selection.toggle('b');
    selection.toggleAll();

    expect(selection.selectedIds.value).toEqual(['a', 'b', 'c']);
  });

  it('clears the selection', () => {
    const selection = inScope(() => useItemSelection(['a', 'b']));

    selection.toggleAll();
    selection.clear();

    expect(selection.count.value).toBe(0);
    expect(selection.isSelected('a')).toBe(false);
  });

  it('follows the list: a log a filter hides is not reported, and is back when the filter is lifted', () => {
    const visible = ref(['a', 'b', 'c']);
    const selection = inScope(() => useItemSelection(visible));

    selection.toggleAll();
    visible.value = ['b'];

    expect(selection.selectedIds.value).toEqual(['b']);
    expect(selection.count.value).toBe(1);
    expect(selection.state.value).toBe(SELECTION_STATE.All);

    visible.value = ['a', 'b', 'c'];

    expect(selection.selectedIds.value).toEqual(['a', 'b', 'c']);
  });

  it('selects only what the list shows now when "all" is chosen under a filter', () => {
    const visible = ref(['a', 'b', 'c']);
    const selection = inScope(() => useItemSelection(visible));

    visible.value = ['a', 'b'];
    selection.toggleAll();
    visible.value = ['a', 'b', 'c'];

    expect(selection.selectedIds.value).toEqual(['a', 'b']);
    expect(selection.state.value).toBe(SELECTION_STATE.Some);
  });

  it('takes a getter, as a screen gives it its list', () => {
    const logs = ref([{ id: 'a' }, { id: 'b' }]);
    const selection = inScope(() => useItemSelection(() => logs.value.map((log) => log.id)));

    selection.toggle('b');
    expect(selection.selectedIds.value).toEqual(['b']);

    logs.value = [{ id: 'b' }, { id: 'a' }];
    selection.toggle('a');
    expect(selection.selectedIds.value).toEqual(['b', 'a']);
  });
});
