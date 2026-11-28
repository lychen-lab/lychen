import { describe, expect, it } from 'vitest';

import {
  SELECTION_STATE,
  selectedInOrder,
  selectionStateOf,
  toggleAllInSelection,
  toggleInSelection,
} from './itemSelection';

describe('toggleInSelection', () => {
  it('selects an item that is not selected', () => {
    expect([...toggleInSelection(new Set(['a']), 'b')]).toEqual(['a', 'b']);
  });

  it('unselects an item that is selected', () => {
    expect([...toggleInSelection(new Set(['a', 'b']), 'a')]).toEqual(['b']);
  });

  it('gives a new set and leaves the one it is given as it was', () => {
    const selected = new Set(['a']);
    const next = toggleInSelection(selected, 'b');

    expect(next).not.toBe(selected);
    expect([...selected]).toEqual(['a']);
  });
});

describe('selectedInOrder', () => {
  it('follows the order of the list, not the order of the taps', () => {
    expect(selectedInOrder(['a', 'b', 'c', 'd'], new Set(['d', 'b']))).toEqual(['b', 'd']);
  });

  it('leaves out what the list does not show', () => {
    expect(selectedInOrder(['a', 'b'], new Set(['a', 'z']))).toEqual(['a']);
  });

  it('is empty when nothing is selected, or the list is', () => {
    expect(selectedInOrder(['a', 'b'], new Set())).toEqual([]);
    expect(selectedInOrder([], new Set(['a']))).toEqual([]);
  });
});

describe('selectionStateOf', () => {
  it('says none, some or all of the list', () => {
    const ids = ['a', 'b', 'c'];

    expect(selectionStateOf(ids, new Set())).toBe(SELECTION_STATE.None);
    expect(selectionStateOf(ids, new Set(['b']))).toBe(SELECTION_STATE.Some);
    expect(selectionStateOf(ids, new Set(['a', 'b', 'c']))).toBe(SELECTION_STATE.All);
  });

  it('only looks at what the list shows', () => {
    expect(selectionStateOf(['a', 'b'], new Set(['z']))).toBe(SELECTION_STATE.None);
    expect(selectionStateOf(['a', 'b'], new Set(['a', 'b', 'z']))).toBe(SELECTION_STATE.All);
  });

  it('has nothing selected in an empty list', () => {
    expect(selectionStateOf([], new Set())).toBe(SELECTION_STATE.None);
    expect(selectionStateOf([], new Set(['a']))).toBe(SELECTION_STATE.None);
  });
});

describe('toggleAllInSelection', () => {
  it('selects the whole list when nothing or only some of it is selected', () => {
    expect([...toggleAllInSelection(['a', 'b', 'c'], new Set())]).toEqual(['a', 'b', 'c']);
    expect([...toggleAllInSelection(['a', 'b', 'c'], new Set(['b']))].sort()).toEqual([
      'a',
      'b',
      'c',
    ]);
  });

  it('unselects the whole list when all of it is selected', () => {
    expect([...toggleAllInSelection(['a', 'b'], new Set(['a', 'b']))]).toEqual([]);
  });

  it('leaves alone what is selected outside the list', () => {
    expect([...toggleAllInSelection(['a', 'b'], new Set(['z']))].sort()).toEqual(['a', 'b', 'z']);
    expect([...toggleAllInSelection(['a', 'b'], new Set(['a', 'b', 'z']))]).toEqual(['z']);
  });

  it('does nothing for an empty list, and does not change the set it is given', () => {
    const selected = new Set(['a']);

    expect([...toggleAllInSelection([], selected)]).toEqual(['a']);
    toggleAllInSelection(['b'], selected);
    expect([...selected]).toEqual(['a']);
  });
});
