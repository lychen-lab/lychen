import { MUSHROOM_LOG_STATE, type MushroomLogState } from './MushroomLog';

/**
 * What narrows the list of logs (spec: "filtrable par zone, état, espèce et essence, recherche
 * par étiquette"). Every filter is optional: the list without any shows every log. An id is the
 * one of the concept it filters on (`Zone`, `FungusSpecies`, `WoodSpecies`).
 */
export interface MushroomLogListFilters {
  /** Part of a label, as typed and trimmed: `B-01` finds `B-012`. */
  label?: string;
  zone?: string;
  state?: MushroomLogState;
  /** The species of the strain the log was inoculated with. */
  fungusSpecies?: string;
  woodSpecies?: string;
}

/**
 * The query parameter of each filter. The URL carries the filters (a filtered list can be shared,
 * reloaded, and left with the back button), under the filters' own names.
 */
export const MUSHROOM_LOG_LIST_QUERY_KEY = {
  Label: 'label',
  Zone: 'zone',
  State: 'state',
  FungusSpecies: 'fungusSpecies',
  WoodSpecies: 'woodSpecies',
} as const satisfies Record<string, keyof MushroomLogListFilters>;

/** A value of a query, as Vue Router gives it: a parameter given twice is an array. */
type QueryValue = string | null | undefined | readonly (string | null | undefined)[];

/** The query of a route: what Vue Router's `route.query` is, and what `router.push` takes. */
export type MushroomLogListQuery = Readonly<Record<string, QueryValue>>;

const STATES: readonly string[] = Object.values(MUSHROOM_LOG_STATE);

/** The first value of a parameter that says something, trimmed: blank is the same as absent. */
function firstText(value: QueryValue): string | undefined {
  const values = Array.isArray(value) ? value : [value];

  for (const candidate of values) {
    const text = typeof candidate === 'string' ? candidate.trim() : '';

    if (text !== '') {
      return text;
    }
  }

  return undefined;
}

function isState(value: string): value is MushroomLogState {
  return STATES.includes(value);
}

/**
 * The filters a URL says. It never throws and never keeps what it does not understand: a
 * parameter that is blank, or a `state` this front does not know (an old link, a typo), is as if
 * it were not there, so that the list opens unfiltered on it rather than empty or broken.
 */
export function parseMushroomLogListQuery(query: MushroomLogListQuery): MushroomLogListFilters {
  const filters: MushroomLogListFilters = {};

  const label = firstText(query[MUSHROOM_LOG_LIST_QUERY_KEY.Label]);
  const zone = firstText(query[MUSHROOM_LOG_LIST_QUERY_KEY.Zone]);
  const state = firstText(query[MUSHROOM_LOG_LIST_QUERY_KEY.State]);
  const fungusSpecies = firstText(query[MUSHROOM_LOG_LIST_QUERY_KEY.FungusSpecies]);
  const woodSpecies = firstText(query[MUSHROOM_LOG_LIST_QUERY_KEY.WoodSpecies]);

  if (label !== undefined) {
    filters.label = label;
  }
  if (zone !== undefined) {
    filters.zone = zone;
  }
  if (state !== undefined && isState(state)) {
    filters.state = state;
  }
  if (fungusSpecies !== undefined) {
    filters.fungusSpecies = fungusSpecies;
  }
  if (woodSpecies !== undefined) {
    filters.woodSpecies = woodSpecies;
  }

  return filters;
}

/**
 * The query that says these filters, with only the filters that are set (a clean URL for the
 * unfiltered list). The inverse of `parseMushroomLogListQuery`.
 */
export function toMushroomLogListQuery(filters: MushroomLogListFilters): Record<string, string> {
  const query: Record<string, string> = {};
  const label = filters.label?.trim();

  if (label) {
    query[MUSHROOM_LOG_LIST_QUERY_KEY.Label] = label;
  }
  if (filters.zone) {
    query[MUSHROOM_LOG_LIST_QUERY_KEY.Zone] = filters.zone;
  }
  if (filters.state) {
    query[MUSHROOM_LOG_LIST_QUERY_KEY.State] = filters.state;
  }
  if (filters.fungusSpecies) {
    query[MUSHROOM_LOG_LIST_QUERY_KEY.FungusSpecies] = filters.fungusSpecies;
  }
  if (filters.woodSpecies) {
    query[MUSHROOM_LOG_LIST_QUERY_KEY.WoodSpecies] = filters.woodSpecies;
  }

  return query;
}

/**
 * A route's query with its filters replaced by these ones, and every other parameter kept: for
 * `router.replace({ query: withMushroomLogListFilters(route.query, filters) })`. A filter that is
 * not set leaves no stale parameter behind.
 */
export function withMushroomLogListFilters(
  query: MushroomLogListQuery,
  filters: MushroomLogListFilters,
): Record<string, QueryValue> {
  const filterKeys: readonly string[] = Object.values(MUSHROOM_LOG_LIST_QUERY_KEY);
  const others = Object.entries(query).filter(([key]) => !filterKeys.includes(key));

  return { ...Object.fromEntries(others), ...toMushroomLogListQuery(filters) };
}

/**
 * How many filters of the filter sheet are set: what the badge of its button says. The label
 * search has its own field, outside the sheet, so it is not counted.
 */
export function countMushroomLogListFilters(filters: MushroomLogListFilters): number {
  return [filters.zone, filters.state, filters.fungusSpecies, filters.woodSpecies].filter(Boolean)
    .length;
}

/**
 * Whether the list is narrowed by anything, the label search included: it tells "no log yet"
 * (an empty state that invites to create one) from "no log matches" (one that invites to clear
 * the filters).
 */
export function hasMushroomLogListFilters(filters: MushroomLogListFilters): boolean {
  return countMushroomLogListFilters(filters) > 0 || Boolean(filters.label?.trim());
}
