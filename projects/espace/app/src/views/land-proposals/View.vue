<template>
  <section class="flex flex-col gap-6">
    <div class="flex flex-col gap-1">
      <h2 class="text-2xl font-bold">Espaces proposés</h2>
      <p class="text-on-surface/60 text-sm">{{ fakeLandProposals.length }} terrains disponibles</p>
    </div>
    <div class="grid grid-cols-2 gap-3">
      <RouterLink
        v-for="proposal in fakeLandProposals"
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
import { ref } from 'vue';
import { RouterLink } from 'vue-router';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import { MESSAGES, TRANSLATION_KEY } from './i18n';
import { Card } from '@lychen/vue-components-business/land-proposal/card';
import { ROUTE_LAND_PROPOSAL } from '@/views/land-proposal';

const { t } = useI18nExtended({ messages: MESSAGES, rootKey: TRANSLATION_KEY, prefixed: true });

const favorites = ref(new Set<string>(['3']));

function toggleFavorite(uuid: string) {
  if (favorites.value.has(uuid)) {
    favorites.value.delete(uuid);
  } else {
    favorites.value.add(uuid);
  }
}

const fakeLandProposals = [
  {
    uuid: '1',
    title: 'Terrain de culture',
    description:
      'Grand terrain ensoleillé idéal pour la culture maraîchère. Sol argileux riche en nutriments, eau disponible sur place.',
    surface: 130,
    altitude: 678,
    city: 'Lille',
    image: 'https://images.pexels.com/photos/59321/pexels-photo-59321.jpeg',
  },
  {
    uuid: '2',
    title: 'Cave à champignons',
    description:
      'Cave naturelle fraîche et humide, parfaite pour la culture de champignons en toute saison.',
    surface: 40,
    altitude: 150,
    city: 'Toulouse',
    image: 'https://images.pexels.com/photos/2499862/pexels-photo-2499862.jpeg',
  },
  {
    uuid: '3',
    title: 'Jardin partagé',
    description:
      'Espace vert urbain divisé en plusieurs parcelles. Accès facile, composteur et outils disponibles sur place.',
    surface: 220,
    altitude: 45,
    city: 'Lyon',
    image: 'https://images.pexels.com/photos/1084540/pexels-photo-1084540.jpeg',
  },
  {
    uuid: '4',
    title: 'Serre horticole',
    description:
      'Serre en verre chauffée, idéale pour les cultures tropicales ou les semis précoces de printemps.',
    surface: 75,
    altitude: 320,
    city: 'Bordeaux',
    image: 'https://images.pexels.com/photos/1389460/pexels-photo-1389460.jpeg',
  },
  {
    uuid: '5',
    title: 'Verger familial',
    description:
      'Verger de 30 arbres fruitiers : pommiers, poiriers, pruniers. Récolte partagée entre propriétaire et cultivateur.',
    surface: 500,
    altitude: 210,
    city: 'Strasbourg',
    image: 'https://images.pexels.com/photos/213399/pexels-photo-213399.jpeg',
  },
  {
    uuid: '6',
    title: 'Balcon potager',
    description:
      'Grand balcon exposé plein sud, idéal pour les herbes aromatiques, tomates cerises et légumes en pots.',
    surface: 12,
    altitude: 28,
    city: 'Paris',
    image: 'https://images.pexels.com/photos/4750270/pexels-photo-4750270.jpeg',
  },
];
</script>

<style lang="css" scoped></style>
