<template>
  <section class="flex flex-col gap-6">
    <div class="flex flex-col gap-1">
      <h2 class="text-2xl font-bold">Espaces proposés</h2>
      <p class="text-on-surface/60 text-sm">
        {{ proposals.length }} terrain{{ proposals.length !== 1 ? 's' : '' }} disponible{{
          proposals.length !== 1 ? 's' : ''
        }}
      </p>
    </div>

    <p
      v-if="isPending"
      class="text-on-surface/60 text-sm"
    >
      Chargement des terrains…
    </p>
    <p
      v-else-if="isError"
      class="text-sm text-red-700"
    >
      Impossible de charger les terrains.
    </p>
    <p
      v-else-if="proposals.length === 0"
      class="text-on-surface/60 text-sm"
    >
      Aucun terrain disponible pour le moment.
    </p>
    <div
      v-else
      class="grid grid-cols-2 gap-3"
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
          :is-favorite="favorites.has(proposal.uuid)"
          class="w-full max-w-full! min-w-0!"
          @favorite.prevent="toggleFavorite(proposal.uuid)"
        />
      </RouterLink>
    </div>
  </section>
</template>

<script lang="ts" setup>
import { computed, ref } from 'vue';
import { RouterLink } from 'vue-router';
import { useQuery } from '@tanstack/vue-query';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import { useEspaceApi } from '@lychen/vue-espace/composables/use-espace-api/useEspaceApi';
import { Card } from '@lychen/vue-components-business/land-proposal/card';
import type { LandProposal } from '@lychen/vue-components-business/land-proposal/card';
import { MESSAGES, TRANSLATION_KEY } from './i18n';
import { ROUTE_LAND_PROPOSAL } from '@/views/land-proposal';

// Image de remplacement en attendant la modélisation des médias côté API (lot 2).
const PLACEHOLDER_IMAGE = 'https://images.pexels.com/photos/59321/pexels-photo-59321.jpeg';

useI18nExtended({ messages: MESSAGES, rootKey: TRANSLATION_KEY, prefixed: true });

const { api } = useEspaceApi();

const { data, isPending, isError } = useQuery({
  queryKey: ['area-proposals'],
  queryFn: async () => {
    const response = await api.GET('/api/area_proposals');
    return response.data;
  },
});

const proposals = computed<LandProposal[]>(
  () =>
    data.value?.member?.map((proposal) => ({
      uuid: proposal.uuid ?? '',
      title: proposal.title ?? '',
      description: proposal.description ?? '',
      surface: proposal.surfaceToShare ?? 0,
      altitude: proposal.altitude ?? 0,
      city: proposal.city ?? undefined,
      image: PLACEHOLDER_IMAGE,
    })) ?? [],
);

const favorites = ref(new Set<string>());

function toggleFavorite(uuid: string) {
  if (favorites.value.has(uuid)) {
    favorites.value.delete(uuid);
  } else {
    favorites.value.add(uuid);
  }
}
</script>

<style lang="css" scoped></style>
