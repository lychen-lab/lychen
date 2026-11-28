import { type ObjectValues } from '@lychen/typescript-utils/transformers/ObjectValues';

/** How much of a list is selected: what the "select all" checkbox shows (unchecked, dash, checked). */
export const SELECTION_STATE = {
  None: 'none',
  /** At least one item and not all of them: the checkbox is indeterminate. */
  Some: 'some',
  All: 'all',
} as const;

export type SelectionState = ObjectValues<typeof SELECTION_STATE>;

/**
 * Selecting several items of a list (the logs to give a bulk entry or to print labels for) is
 * these few functions, on a set of ids. They never change the set they are given, so that a
 * `shallowRef` of it notices every change.
 */

/** The selection with `id` added, or removed if it was selected. */
export function toggleInSelection(selected: ReadonlySet<string>, id: string): Set<string> {
  const next = new Set(selected);

  if (!next.delete(id)) {
    next.add(id);
  }

  return next;
}

/** The selected ones among `visibleIds`, in the order of the list: the order a bulk entry follows. */
export function selectedInOrder(
  visibleIds: readonly string[],
  selected: ReadonlySet<string>,
): string[] {
  return visibleIds.filter((id) => selected.has(id));
}

/** How much of `visibleIds` is selected. An empty list has nothing selected. */
export function selectionStateOf(
  visibleIds: readonly string[],
  selected: ReadonlySet<string>,
): SelectionState {
  const count = selectedInOrder(visibleIds, selected).length;

  if (count === 0) {
    return SELECTION_STATE.None;
  }

  return count === visibleIds.length ? SELECTION_STATE.All : SELECTION_STATE.Some;
}

/**
 * What "select all" does: selects every item of `visibleIds`, or, when they are all selected
 * already, unselects them. What is selected outside `visibleIds` is left as it is.
 */
export function toggleAllInSelection(
  visibleIds: readonly string[],
  selected: ReadonlySet<string>,
): Set<string> {
  const next = new Set(selected);
  const everyoneIsSelected = selectionStateOf(visibleIds, selected) === SELECTION_STATE.All;

  for (const id of visibleIds) {
    if (everyoneIsSelected) {
      next.delete(id);
    } else {
      next.add(id);
    }
  }

  return next;
}
