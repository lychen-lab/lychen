<template>
  <Container class="flex flex-col items-center gap-8 pt-20 text-center">
    <Title variant="h1">{{ t('title') }}</Title>
    <Paragraph>{{ t('description') }}</Paragraph>
  </Container>

  <Container class="flex flex-col items-center gap-8 pt-20 text-center">
    <Title variant="h2">{{ t('goals.title') }}</Title>
    <Paragraph>{{ t('goals.description') }}</Paragraph>
    <div class="grid grid-cols-2 grid-rows-2 items-stretch gap-8">
      <div class="row-span-2 flex flex-col items-center justify-center gap-4 rounded-2xl">
        <img />
        <Title variant="h3">Augmenter la production alimentaire locale</Title>
        <p class="opacity-60">
          Fournir les outils, les connaissances et les données pour développer la production
          alimentaire locale en respectant la biodiversité locale.
        </p>
      </div>
      <div class="flex flex-col items-center justify-center gap-4 rounded-2xl">
        <Title variant="h3">Développer et protéger la biodiversité locale</Title>
        <p>
          Permettre aux acteurs locaux d'observer, quantifier et protéger la biodiversité locale.
        </p>
      </div>
      <div class="flex flex-col items-center justify-center gap-4 rounded-2xl">
        <Title variant="h3">Renforcer le lien social et économique</Title>
        <p>
          Utiliser les données et l'informatique pour favoriser la coopération et la solidarité.
        </p>
      </div>
    </div>
  </Container>

  <Container class="flex flex-col items-center gap-8 pt-20 text-center">
    <Title variant="h2">{{ t('status.title') }}</Title>
    <Paragraph>{{ t('status.description') }}</Paragraph>
    <p class="opacity-60">
      {{ t('status.content') }}
    </p>
  </Container>
</template>

<script lang="ts" setup>
import { usePrefixedI18n } from '@lychen/vue-i18n/composables/useI18nExtended';
import { CONFIG } from './i18n';
import Container from '@lychen/vue-components-website/container/Container.vue';
import Title from '@lychen/vue-components-website/title/Title.vue';
import Paragraph from '@lychen/vue-components-website/paragraph/Paragraph.vue';

const { t } = usePrefixedI18n(CONFIG);
</script>
