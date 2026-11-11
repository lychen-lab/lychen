// What every endpoint's mapper needs to turn the API's representations into the front's
// models (libs/typescript/espace/models) and the front's inputs into request bodies.

import type { Collection } from '@lychen/typescript-espace/models/Collection';
import type { PersonName } from '@lychen/typescript-espace/models/PersonName';

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
    throw new Error(`espace-api: the response has no ${field}.`);
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

/** A first and a last name, or undefined when the API gives neither. */
export function toPersonName(
  firstName: string | null | undefined,
  lastName: string | null | undefined,
): PersonName | undefined {
  if (!firstName && !lastName) {
    return undefined;
  }
  return { firstName: optional(firstName), lastName: optional(lastName) };
}

/** The part of a Hydra collection page a Collection is built from. */
interface HydraPage<Item> {
  member: Item[];
  totalItems?: number;
  view?: { next?: string | null };
}

/** A Hydra collection page, its members mapped to the front's model. */
export function toCollection<Item, Model>(
  page: HydraPage<Item>,
  mapItem: (item: Item) => Model,
): Collection<Model> {
  return {
    items: page.member.map((item) => mapItem(item)),
    totalItems: page.totalItems ?? page.member.length,
    hasNextPage: Boolean(page.view?.next),
  };
}

/**
 * The object without its undefined properties: a field the front leaves out is not sent,
 * so that a JSON Merge Patch keeps it as it is and a query string does not carry it.
 */
export function withoutUndefined<T extends object>(object: T): T {
  return Object.fromEntries(Object.entries(object).filter(([, value]) => value !== undefined)) as T;
}
