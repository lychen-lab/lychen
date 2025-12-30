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
  <Container>
    <Title variant="h2">Derniers terrains disponibles</Title>
    <div class="flex flex-row gap-4">
      <CardLandProposal
        v-for="item in fakeLandProposals"
        :key="item.id"
        v-bind="item"
      />
    </div>
  </Container>
</template>

<script lang="ts" setup>
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import { Card as CardLandProposal } from '@lychen/vue-components-business/land-proposal/card';
import { MESSAGES, TRANSLATION_KEY } from './i18n';
import { Container } from '@lychen/vue-components-website/container';
import { Title } from '@lychen/vue-components-website/title';
import { Paragraph } from '@lychen/vue-components-website/paragraph';
import { Input } from '@lychen/vue-components-core/input';
import { Button } from '@lychen/vue-components-core/button';
import IconSearch from '@lychen/vue-icons/IconSearch.vue';

const { t } = useI18nExtended({ messages: MESSAGES, rootKey: TRANSLATION_KEY, prefixed: true });

const fakeLandProposals = [
  {
    id: '1',
  },
  {
    id: '2',
  },
  {
    id: '3',
  },
];
</script>

<style lang="css" scoped></style>
