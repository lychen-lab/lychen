<template>
  <header class="flex flex-row items-center font-lexend">
    <span class="font-medium tracking-normal text-on-surface">espace</span>
    <span class="text-on-surface/40">.lychen</span>
  </header>
  <section class="relative flex h-125 flex-col gap-4 rounded-4xl">
    <img
      src="https://res.cloudinary.com/ddhvfiezg/image/upload/v1768572225/019bc71e-9a9d-7b3c-9478-f855ba60f20c.jpg"
      alt=""
      class="absolute z-2 size-full rounded-4xl object-cover"
      decoding="async"
    />
    <div class="z-3 flex h-full flex-col justify-end gap-2 p-6">
      <h1 class="text-4xl font-bold text-white">{{ t('hero.title') }}</h1>
      <p class="text-md text-white opacity-80">{{ t('hero.description') }}</p>
    </div>
  </section>
  <section class="flex flex-col gap-4">
    <h2 class="text-xl font-bold">{{ t('start.title') }}</h2>
    <div class="grid grid-cols-2 gap-2">
      <RouterLink
        :to="{ name: ROUTE_LAND_REQUESTS.name }"
        class="flex flex-col justify-between gap-10 rounded-2xl bg-amber-700 p-6 text-amber-50"
      >
        <IconFence />
        <div>
          <p class="text-lg leading-5 font-bold">{{ t('start.share.title') }}</p>
          <small class="text-xs opacity-70">{{ t('start.share.description') }}</small>
        </div>
      </RouterLink>
      <RouterLink
        :to="{ name: ROUTE_LAND_PROPOSALS.name }"
        class="flex flex-col justify-between gap-10 rounded-2xl bg-emerald-700 p-6 text-emerald-50"
      >
        <IconSearch />
        <div>
          <p class="text-lg leading-5 font-bold">{{ t('start.search.title') }}</p>
          <small class="text-xs opacity-70">{{ t('start.search.description') }}</small>
        </div>
      </RouterLink>
    </div>
  </section>
  <section class="flex flex-col gap-6">
    <h2 class="text-center text-xl font-bold">{{ t('how.title') }}</h2>
    <SectionLuk
      :title="t('how.discover.title')"
      :description="t('how.discover.description')"
    >
      <template #icon>
        <IconCompass />
      </template>
    </SectionLuk>
    <SectionLuk
      :title="t('how.connect.title')"
      :description="t('how.connect.description')"
    >
      <template #icon>
        <IconHeartHandshake />
      </template>
    </SectionLuk>
    <SectionLuk
      :title="t('how.grow.title')"
      :description="t('how.grow.description')"
    >
      <template #icon>
        <IconSprout />
      </template>
    </SectionLuk>
  </section>
  <section class="flex flex-col gap-2">
    <div class="flex flex-row items-center justify-between gap-2">
      <h2 class="text-xl font-bold">{{ t('latest.title') }}</h2>
      <RouterLink :to="{ name: ROUTE_LAND_PROPOSALS.name }">
        <Button size="sm">{{ t('latest.see_all') }}</Button>
      </RouterLink>
    </div>
    <QueryState
      :is-pending="isPending"
      :is-error="isError"
      :is-empty="proposals.length === 0"
      :empty-text="t('latest.empty')"
      :error-text="t('latest.error')"
      @retry="refetch()"
    >
      <div class="-mx-4 flex flex-row gap-4 overflow-x-auto px-4 py-2">
        <RouterLink
          v-for="proposal in proposals"
          :key="proposal.uuid"
          :to="{ name: ROUTE_LAND_PROPOSAL.name, params: { uuid: proposal.uuid } }"
          class="flex"
        >
          <Card
            v-bind="proposal"
            :clickable="true"
            :display-city="true"
          />
        </RouterLink>
      </div>
    </QueryState>
  </section>
</template>

<script lang="ts" setup>
import { computed } from 'vue';
import { RouterLink } from 'vue-router';
import { AREA_PROPOSAL_STATE } from '@lychen/typescript-front-model-business/area/AreaProposal';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import { useGetAreaProposals } from '@lychen/vue-espace-api/endpoint/area-proposal/get-area-proposals';
import { Card } from '@lychen/vue-components-business/land-proposal/card';
import Button from '@lychen/vue-components-core/button/Button.vue';
import IconCompass from '@lychen/vue-icons/IconCompass.vue';
import IconFence from '@lychen/vue-icons/IconFence.vue';
import IconHeartHandshake from '@lychen/vue-icons/IconHeartHandshake.vue';
import IconSearch from '@lychen/vue-icons/IconSearch.vue';
import IconSprout from '@lychen/vue-icons/IconSprout.vue';
import QueryState from '@/components/QueryState.vue';
import { toLandProposalCard } from '@/helpers/cards';
import SectionLuk from '@/views/home/SectionLuk.vue';
import { ROUTE_LAND_PROPOSAL } from '@/views/land-proposal';
import { ROUTE_LAND_PROPOSALS } from '@/views/land-proposals';
import { ROUTE_LAND_REQUESTS } from '@/views/land-requests';
import { MESSAGES, TRANSLATION_KEY } from './i18n';

const LATEST_COUNT = 6;

const { t } = useI18nExtended({ messages: MESSAGES, rootKey: TRANSLATION_KEY, prefixed: true });

// Live: a proposal published, edited or deleted while the screen is open refetches them.
const { data, isPending, isError, refetch } = useGetAreaProposals(
  {
    place: AREA_PROPOSAL_STATE.Published,
    'order[createdAt]': 'desc',
    itemsPerPage: LATEST_COUNT,
  },
  { live: true },
);

const proposals = computed(() => data.value?.member.map(toLandProposalCard) ?? []);
</script>
