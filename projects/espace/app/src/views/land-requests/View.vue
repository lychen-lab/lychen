<template>
  <section class="flex flex-col gap-6">
    <div class="flex flex-col gap-1">
      <h1 class="text-2xl font-bold">{{ t('title') }}</h1>
      <p
        v-if="data"
        class="text-sm text-on-surface/60"
      >
        {{ t('count', totalItems) }}
      </p>
    </div>

    <CollectionFilters
      :filters="filters"
      :has-active-filters="hasActiveFilters"
      @update="update"
      @reset="reset"
    />

    <QueryState
      :is-pending="isPending"
      :is-error="isError"
      :is-empty="requests.length === 0"
      :empty-text="hasActiveFilters ? t('empty_filtered') : t('empty')"
      :error-text="t('error')"
      @retry="refetch()"
    >
      <div
        class="flex flex-col gap-3 transition-opacity"
        :class="{ 'opacity-50': isPlaceholderData }"
      >
        <RouterLink
          v-for="request in requests"
          :key="request.uuid"
          :to="{ name: ROUTE_LAND_REQUEST.name, params: { uuid: request.uuid } }"
        >
          <Card
            v-bind="request"
            class="w-full max-w-full! min-w-0!"
          />
        </RouterLink>
      </div>
      <CollectionPagination
        :page="filters.page"
        :total-items="totalItems"
        @update:page="goToPage"
      />
    </QueryState>
  </section>
</template>

<script lang="ts" setup>
import { computed } from 'vue';
import { RouterLink } from 'vue-router';
import { keepPreviousData, useQuery } from '@tanstack/vue-query';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import { useEspaceApi } from '@lychen/vue-espace/composables/use-espace-api/useEspaceApi';
import { Card } from '@lychen/vue-components-business/land-request/card';
import CollectionFilters from '@/components/CollectionFilters.vue';
import CollectionPagination from '@/components/CollectionPagination.vue';
import QueryState from '@/components/QueryState.vue';
import { useCollectionFilters } from '@/composables/useCollectionFilters';
import { PLACE_ACTIVE, toLandRequestCard } from '@/helpers/cards';
import { ROUTE_LAND_REQUEST } from '@/views/land-request';
import { MESSAGES, TRANSLATION_KEY } from './i18n';

const { t } = useI18nExtended({ messages: MESSAGES, rootKey: TRANSLATION_KEY, prefixed: true });

const { api } = useEspaceApi();
const { filters, hasActiveFilters, apiQuery, update, reset } = useCollectionFilters();

// No live updates here: the API only publishes area proposals on Mercure.
const { data, isPending, isError, isPlaceholderData, refetch } = useQuery({
  queryKey: ['area-requests', 'list', apiQuery],
  queryFn: async () => {
    // The collection also returns the user's own drafts: this screen is the public listing.
    const response = await api.apiAreaRequestsGetCollection({
      query: { ...apiQuery.value, place: PLACE_ACTIVE },
    });
    return response.data;
  },
  // Keep the current page on screen while the next one, or a new search, loads.
  placeholderData: keepPreviousData,
});

const requests = computed(() => data.value?.member.map(toLandRequestCard) ?? []);
const totalItems = computed(() => data.value?.totalItems ?? 0);

async function goToPage(page: number) {
  await update({ page });
  window.scrollTo({ top: 0 });
}
</script>
