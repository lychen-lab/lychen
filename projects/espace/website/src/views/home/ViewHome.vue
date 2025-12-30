<template>
  <Container class="flex flex-col items-center justify-center gap-4 text-center">
    <Title variant="h1">Trouvez ou partagez votre espace de culture</Title>
    <Paragraph
      >Rechercher un terrain ou partager votre espace pour cultiver des légumes, faire du jardinage
      et créer du lien.</Paragraph
    >
    <div class="flex w-2/3 flex-row items-center gap-2">
      <Input placeholder="Rechercher par ville" />
      <Button
        icon-only
        size="sm"
        ><template #icon><IconSearch /></template
      ></Button>
    </div>
    <div>Map</div>
  </Container>
  <Container class="flex flex-col items-center justify-center gap-8 text-center">
    <Title variant="h2">Derniers terrains disponibles</Title>
    <div class="flex flex-row gap-4">
      <CardLandProposal
        v-for="item in fakeLandProposals"
        :key="item.uuid"
        v-bind="item"
        display-city
      />
    </div>
    <Button
      >Plus de terrains <template #icon><IconArrowRight /></template
    ></Button>
  </Container>
</template>

<script lang="ts" setup>
import { usePrefixedI18n } from '@lychen/vue-i18n/composables/useI18nExtended';
import {
  Card as CardLandProposal,
  type LandProposal,
} from '@lychen/vue-components-business/land-proposal/card';
import { Container } from '@lychen/vue-components-website/container';
import { Title } from '@lychen/vue-components-website/title';
import { Paragraph } from '@lychen/vue-components-website/paragraph';
import { Input } from '@lychen/vue-components-core/input';
import { Button } from '@lychen/vue-components-core/button';
import IconSearch from '@lychen/vue-icons/IconSearch.vue';
import IconArrowRight from '@lychen/vue-icons/IconArrowRight.vue';
import { CONFIG } from './i18n';

const { t } = usePrefixedI18n(CONFIG);

const fakeLandProposals: LandProposal[] = [
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
  {
    uuid: '3',
    title: 'Fleurs et arbres',
    description: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
    surface: 75,
    altitude: 0,
    city: 'Lyon',
    image: 'https://images.pexels.com/photos/131772/pexels-photo-131772.jpeg',
  },
  {
    uuid: '4',
    title: 'Terrain de culture',
    description: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
    surface: 37,
    altitude: 345,
    city: 'Grenoble',
    image: 'https://images.pexels.com/photos/1084540/pexels-photo-1084540.jpeg',
  },
];
</script>

<style lang="css" scoped></style>
