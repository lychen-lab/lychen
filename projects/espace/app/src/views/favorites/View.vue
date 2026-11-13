<template>
  <section class="flex flex-col gap-6">
    <div class="flex flex-col gap-1">
      <h1 class="text-2xl font-bold">{{ t('title') }}</h1>
      <p class="text-sm text-on-surface/60">{{ t('count', favorites.length) }}</p>
      <p class="text-xs text-on-surface/60">{{ t('local_only') }}</p>
    </div>

    <div
      v-if="favorites.length === 0"
      class="flex flex-col items-center gap-4 py-16 text-center"
    >
      <div class="flex size-16 items-center justify-center rounded-full bg-surface-container">
        <IconHeart class="size-8 text-on-surface/40" />
      </div>
      <div class="flex flex-col gap-1">
        <p class="font-semibold">{{ t('empty.title') }}</p>
        <p class="text-sm text-on-surface/60">{{ t('empty.description') }}</p>
      </div>
      <RouterLink :to="{ name: ROUTE_LAND_PROPOSALS.name }">
        <Button>{{ t('empty.action') }}</Button>
      </RouterLink>
    </div>

    <template v-else>
      <div
        v-if="unavailable.length > 0"
        class="flex flex-col items-start gap-2 rounded-2xl bg-surface-container p-4 text-sm"
      >
        <p>{{ t('unavailable.message', unavailable.length) }}</p>
        <Button
          variant="outline"
          size="sm"
          @click="unavailable.forEach(removeFavorite)"
        >
          {{ t('unavailable.action') }}
        </Button>
      </div>

      <QueryState
        :is-pending="isPending"
        :is-error="isError"
        :error-text="t('error')"
        @retry="refetchFailed"
      >
        <div class="grid grid-cols-2 gap-3">
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
              :is-favorite="true"
              class="w-full max-w-full! min-w-0!"
              @favorite="removeFavorite(proposal.uuid)"
            />
          </RouterLink>
        </div>
      </QueryState>
    </template>
  </section>
</template>

<script lang="ts" setup>
import { computed } from 'vue';
import { RouterLink } from 'vue-router';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import { useGetEachAreaProposal } from '@lychen/vue-espace-api/endpoint/area-proposal/get-area-proposal';
import { Card } from '@lychen/vue-components-business/land-proposal/card';
import Button from '@lychen/vue-components-core/button/Button.vue';
import IconHeart from '@lychen/vue-icons/IconHeart.vue';
import QueryState from '@/components/QueryState.vue';
import { useFavorites } from '@/composables/useFavorites';
import { toLandProposalCard } from '@/helpers/cards';
import { isNotFoundError } from '@/helpers/errors';
import { ROUTE_LAND_PROPOSAL } from '@/views/land-proposal';
import { ROUTE_LAND_PROPOSALS } from '@/views/land-proposals';
import { MESSAGES, TRANSLATION_KEY } from './i18n';

const { t } = useI18nExtended({ messages: MESSAGES, rootKey: TRANSLATION_KEY, prefixed: true });

const { favorites, removeFavorite } = useFavorites();

// The API cannot filter the collection by UUID: each favorite is fetched on its own, in the
// cache of the detail screen. Live: a favorite edited, unpublished or deleted while the
// screen is open.
const queries = useGetEachAreaProposal(favorites, {
  live: true,
  retry: (failureCount, failure) => !isNotFoundError(failure) && failureCount < 3,
});

const proposals = computed(() =>
  queries.value.flatMap(({ data }) => (data ? [toLandProposalCard(data)] : [])),
);

// Favorites whose proposal answers 404: deleted, or no longer published.
const unavailable = computed(() =>
  favorites.value.filter((_, index) => isNotFoundError(queries.value[index]?.error)),
);

const failed = computed(() =>
  queries.value.filter(({ isError, error }) => isError && !isNotFoundError(error)),
);
const isPending = computed(
  () => proposals.value.length === 0 && queries.value.some((query) => query.isPending),
);
const isError = computed(() => proposals.value.length === 0 && failed.value.length > 0);

function refetchFailed() {
  failed.value.forEach((query) => query.refetch());
}
</script>
