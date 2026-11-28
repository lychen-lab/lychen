import { computed, shallowRef, toValue, type MaybeRefOrGetter } from 'vue';

import {
  selectedInOrder,
  selectionStateOf,
  toggleAllInSelection,
  toggleInSelection,
} from '@lychen/typescript-myko/selection/itemSelection';

/**
 * The multiple selection of a list (the logs to give a bulk entry or to print labels for), over
 * the ids the list shows now: `useItemSelection(() => logs.value.map((log) => log.id))`.
 *
 * What it says (`selectedIds`, `count`, `state`) only ever concerns the items the list shows, in
 * the order of the list, so that a bulk entry never reaches a log that a filter hides. What it
 * remembers does not: an item that leaves the list for a moment (a filter, a refetch) is still
 * selected when it comes back, until `clear()`, which the screen calls once the entry is done.
 */
export function useItemSelection(visibleIds: MaybeRefOrGetter<readonly string[]>) {
  const selected = shallowRef<ReadonlySet<string>>(new Set());

  const selectedIds = computed(() => selectedInOrder(toValue(visibleIds), selected.value));
  const count = computed(() => selectedIds.value.length);
  /** Of the whole list: for the "select all" checkbox. */
  const state = computed(() => selectionStateOf(toValue(visibleIds), selected.value));

  function isSelected(id: string): boolean {
    return selected.value.has(id);
  }

  function toggle(id: string): void {
    selected.value = toggleInSelection(selected.value, id);
  }

  /** Selects the whole list, or unselects it when it is all selected. */
  function toggleAll(): void {
    selected.value = toggleAllInSelection(toValue(visibleIds), selected.value);
  }

  function clear(): void {
    selected.value = new Set();
  }

  return { selectedIds, count, state, isSelected, toggle, toggleAll, clear };
}
