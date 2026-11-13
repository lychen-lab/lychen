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
      :is-empty="proposals.length === 0"
      :empty-text="hasActiveFilters ? t('empty_filtered') : t('empty')"
      :error-text="t('error')"
      @retry="refetch()"
    >
      <div
        class="grid grid-cols-2 gap-3 transition-opacity"
        :class="{ 'opacity-50': isPlaceholderData }"
      >
        <RouterLink
          v-for="proposal in proposals"
          :key="proposal.uuid"
          :to="{ name: ROUTE_LAND_PROPOSAL.name, params: { uuid: proposal.uuid } }"
          class="contents"
        >
          <Card
            v-bind="proposal"
            :clickable="true"
            :display-city="true"
            :display-favorite-button="true"
            :is-favorite="isFavorite(proposal.uuid)"
            class="w-full max-w-full! min-w-0!"
            @favorite="toggleFavorite(proposal.uuid)"
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
import { keepPreviousData } from '@tanstack/vue-query';
import { AREA_PROPOSAL_STATE } from '@lychen/typescript-front-model-business/area/AreaProposal';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import { useGetAreaProposals } from '@lychen/vue-espace-api/endpoint/area-proposal/get-area-proposals';
import { Card } from '@lychen/vue-components-business/land-proposal/card';
import CollectionFilters from '@/components/CollectionFilters.vue';
import CollectionPagination from '@/components/CollectionPagination.vue';
import QueryState from '@/components/QueryState.vue';
import { useCollectionFilters } from '@/composables/useCollectionFilters';
import { useFavorites } from '@/composables/useFavorites';
import { toLandProposalCard } from '@/helpers/cards';
import { ROUTE_LAND_PROPOSAL } from '@/views/land-proposal';
import { MESSAGES, TRANSLATION_KEY } from './i18n';

const { t } = useI18nExtended({ messages: MESSAGES, rootKey: TRANSLATION_KEY, prefixed: true });

const { filters, hasActiveFilters, apiQuery, update, reset } = useCollectionFilters();
const { isFavorite, toggleFavorite } = useFavorites();

// The collection also returns the user's own drafts: this screen is the public listing.
// Live: any proposal created, edited or deleted, by anyone, refetches the list.
const { data, isPending, isError, isPlaceholderData, refetch } = useGetAreaProposals(
  () => ({ ...apiQuery.value, place: AREA_PROPOSAL_STATE.Published }),
  {
    live: true,
    // Keep the current page on screen while the next one, or a new search, loads.
    placeholderData: keepPreviousData,
  },
);

const proposals = computed(() => data.value?.member.map(toLandProposalCard) ?? []);
const totalItems = computed(() => data.value?.totalItems ?? 0);

async function goToPage(page: number) {
  await update({ page });
  window.scrollTo({ top: 0 });
}
</script>
