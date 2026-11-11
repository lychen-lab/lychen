// What the mappers of every <api>-api Vue lib (libs/vue/<api>-api/endpoint/*/use-*/mapToModel.ts
// and mapToRequest.ts) need to turn an API Platform representation into a front model, and a
// front input into a request body.

import type { Collection, CollectionView } from './Collection';

/** A value the API may send as `null`, as the front's `undefined`. */
export function optional<T>(value: T | null | undefined): T | undefined {
  return value ?? undefined;
}

/**
 * A value the front cannot do without, such as an identifier. A response without it
 * breaks the API's contract: say so here rather than let `undefined` travel further.
 */
export function required<T>(value: T | null | undefined, field: string): T {
  if (value === null || value === undefined) {
    throw new Error(`The API response has no ${field}.`);
  }
  return value;
}

/** An ISO 8601 date-time of the API as a Date. */
export function toDate(value: string | null | undefined): Date | undefined {
  return value ? new Date(value) : undefined;
}

/**
 * The value as one of those the front knows, or undefined for any other, such as a
 * workflow place the API added since.
 */
export function oneOf<T extends string>(
  known: readonly T[],
  value: string | null | undefined,
): T | undefined {
  return known.find((candidate) => candidate === value);
}

/**
 * A list the API gives (an array property, or a response that is a plain array), each item
 * mapped to the front's model. A list the API leaves out or sends as `null` is empty.
 */
export function toArray<Item, Model>(
  items: readonly Item[] | null | undefined,
  mapItem: (item: Item) => Model,
): Model[] {
  return (items ?? []).map((item) => mapItem(item));
}

/** The part of a Hydra collection page a Collection is built from. */
interface HydraPage<Item> {
  member: Item[];
  totalItems?: number;
  view?: {
    first?: string | null;
    last?: string | null;
    previous?: string | null;
    next?: string | null;
  };
}

/**
 * A Hydra collection page as the API gives it, its members mapped to the front's model.
 * What the API leaves out stays out.
 */
export function toCollection<Item, Model>(
  page: HydraPage<Item>,
  mapItem: (item: Item) => Model,
): Collection<Model> {
  return withoutUndefined({
    member: toArray(page.member, mapItem),
    totalItems: page.totalItems,
    view: page.view && toCollectionView(page.view),
  });
}

function toCollectionView(view: NonNullable<HydraPage<unknown>['view']>): CollectionView {
  return withoutUndefined({
    first: optional(view.first),
    last: optional(view.last),
    previous: optional(view.previous),
    next: optional(view.next),
  });
}

/**
 * The object without its undefined properties: a field the front leaves out is not sent,
 * so that a JSON Merge Patch keeps it as it is and a query string does not carry it.
 */
export function withoutUndefined<T extends object>(object: T): T {
  return Object.fromEntries(Object.entries(object).filter(([, value]) => value !== undefined)) as T;
}
