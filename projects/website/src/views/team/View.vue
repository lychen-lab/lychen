<template>
  <Container class="flex flex-col items-center gap-4">
    <Title variant="h1">{{ t('title') }}</Title>
    <Paragraph class="w-3/4 text-center opacity-60">A diverse team of passionate ...</Paragraph>
    <div class="grid h-50 w-full grid-cols-4 grid-rows-3 gap-8">
      <div class="row-span-2 rounded-[60px] bg-green-300"></div>
      <div class="row-span-1 rounded-[60px] bg-green-800"></div>
      <div class="row-span-2 rounded-[60px] bg-pink-300"></div>
      <div class="row-span-1 rounded-[60px] bg-blue-300"></div>
      <div class="row-span-1 rounded-[60px] bg-red-300"></div>
      <div class="row-span-1 rounded-[60px] bg-yellow-300"></div>
    </div>
  </Container>

  <Container class="flex flex-col items-center gap-4">
    <Title variant="h2">The lychen team</Title>
    <Paragraph class="w-3/4 text-center opacity-60">
      Lorem, ipsum dolor sit amet consectetur adipisicing elit. Laudantium quisquam magni maxime
      eligendi sequi, aspernatur placeat ea, blanditiis accusantium laborum molestias quae
      doloremque magnam ducimus quaerat repellat architecto, aliquid alias.
    </Paragraph>
    <div class="grid grid-cols-4 gap-8 pt-20">
      <CardPerson
        v-for="(person, index) in persons"
        :key="index"
        v-bind="person"
      />
    </div>
  </Container>

  <Container class="grid grid-cols-2 gap-8">
    <div>
      <Title variant="h2">Découvrir notre manière de construire l'écosystème</Title>
      <Paragraph class="w-3/4 text-left opacity-60">
        Lorem ipsum, dolor sit amet consectetur adipisicing elit. Commodi sint at atque illum esse
        accusantium necessitatibus, molestias fuga voluptatem voluptatibus provident ut maxime neque
        officia odit iusto eius illo nisi!
      </Paragraph>
    </div>
    <div>
      <img />
    </div>
  </Container>
</template>

<script lang="ts" setup>
import { usePrefixedI18n } from '@lychen/vue-i18n/composables/useI18nExtended';
import { CONFIG } from './i18n';
import Container from '@lychen/vue-components-website/container/Container.vue';
import Title from '@lychen/vue-components-website/title/Title.vue';
import Paragraph from '@lychen/vue-components-website/paragraph/Paragraph.vue';
import persons from './data/team';
import CardPerson from '@/views/team/CardPerson.vue';

const { t } = usePrefixedI18n(CONFIG);
</script>
