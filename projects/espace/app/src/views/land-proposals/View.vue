<template>
  <section class="flex flex-col gap-4">
    <h2 class="text-xl font-bold">Espaces proposés</h2>
  </section>
  <section class="flex flex-col gap-4">
    <LandProposalCard
      v-for="proposal in fakeLandProposals"
      :key="proposal.uuid"
      v-bind="proposal"
    />
  </section>
</template>

<script lang="ts" setup>
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import { MESSAGES, TRANSLATION_KEY } from './i18n';
import { Card as LandProposalCard } from '@lychen/vue-components-business/land-proposal/card';

const { t } = useI18nExtended({ messages: MESSAGES, rootKey: TRANSLATION_KEY, prefixed: true });

const fakeLandProposals = [
  {
    uuid: '1',
    title: 'Terrain de culture',
    description:
      'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam nec metus vel ante feugiat finibus. Nullam nec metus vel ante feugiat finibus.',
    surface: 130,
    altitude: 678,
    city: 'Lille',
    image: 'https://images.pexels.com/photos/59321/pexels-photo-59321.jpeg',
  },
  {
    uuid: '2',
    title: 'Cave à champignons',
    description:
      'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam nec metus vel ante feugiat finibus. Nullam nec metus vel ante feugiat finibus.',
    surface: 40,
    altitude: 150,
    city: 'Toulouse',
    image: 'https://images.pexels.com/photos/2499862/pexels-photo-2499862.jpeg',
  },
];
</script>

<style lang="css" scoped></style>
