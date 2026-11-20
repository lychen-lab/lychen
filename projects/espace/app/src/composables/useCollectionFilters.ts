import { computed } from 'vue';
import { useRoute, useRouter, type LocationQueryValue } from 'vue-router';

export const ITEMS_PER_PAGE = 12;

export type CollectionOrder = 'asc' | 'desc';

export interface CollectionFilters {
  city: string;
  activity: string;
  order: CollectionOrder;
  page: number;
}

function single(value: LocationQueryValue | LocationQueryValue[] | undefined): string {
  return (Array.isArray(value) ? value[0] : value) ?? '';
}

/**
 * Search, filters and page of a listing, kept in the URL (`?city=&activity=&order=&page=`) so
 * a filtered list can be shared, reloaded and navigated back to. Default values are left out
 * of the URL.
 */
export function useCollectionFilters() {
  const route = useRoute();
  const router = useRouter();

  const filters = computed<CollectionFilters>(() => ({
    city: single(route.query.city).trim(),
    activity: single(route.query.activity),
    order: single(route.query.order) === 'asc' ? 'asc' : 'desc',
    page: Math.max(1, Number.parseInt(single(route.query.page), 10) || 1),
  }));

  const hasActiveFilters = computed(
    () =>
      filters.value.city !== '' || filters.value.activity !== '' || filters.value.order !== 'desc',
  );

  /** Changing anything but the page goes back to the first one. */
  function update(changes: Partial<CollectionFilters>) {
    const next = { ...filters.value, page: 1, ...changes };
    return router.replace({
      query: {
        ...route.query,
        city: next.city || undefined,
        activity: next.activity || undefined,
        order: next.order === 'asc' ? 'asc' : undefined,
        page: next.page > 1 ? String(next.page) : undefined,
      },
    });
  }

  function reset() {
    return update({ city: '', activity: '', order: 'desc' });
  }

  /** The filters as the query parameters both listing collections of the API take. */
  const apiQuery = computed(() => ({
    city: filters.value.city || undefined,
    activity: filters.value.activity || undefined,
    'order[createdAt]': filters.value.order,
    page: filters.value.page,
    itemsPerPage: ITEMS_PER_PAGE,
  }));

  return { filters, hasActiveFilters, apiQuery, update, reset };
}
