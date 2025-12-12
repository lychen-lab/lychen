<template>
  <Container class="flex flex-col items-center gap-4 pt-20">
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
    <Paragraph class="text-center opacity-60 md:w-3/4">
      Lorem, ipsum dolor sit amet consectetur adipisicing elit. Laudantium quisquam magni maxime
      eligendi sequi, aspernatur placeat ea, blanditiis accusantium laborum molestias quae
      doloremque magnam ducimus quaerat repellat architecto, aliquid alias.
    </Paragraph>
    <div class="flex flex-row items-center gap-2">
      <small>N'hésitez pas à nous contacter </small>

      <Badge class="bg-stone-200"
        >{{ contactEmail }}
        <IconCopy
          v-if="isSupported"
          class="opacity-60 hover:opacity-100"
          @click="copy(contactEmail)"
      /></Badge>
      <a
        :href="`mailto:${contactEmail}`"
        class="cursor-pointer"
      >
        <Button
          size="xs"
          icon-only
          variant="ghost"
        >
          <template #icon><IconSendHorizontal class="opacity-60 hover:opacity-100" /></template>
        </Button>
      </a>
    </div>
    <div class="grid grid-cols-2 gap-8 pt-20 md:grid-cols-4">
      <CardPerson
        v-for="(person, index) in persons"
        :key="index"
        v-bind="person"
      />
    </div>
  </Container>

  <Container class="flex flex-col gap-8 md:grid md:grid-cols-2">
    <div class="flex flex-col items-start gap-8">
      <Title variant="h2">Découvrir notre manière de construire l'écosystème</Title>
      <Paragraph class="w-3/4 text-left opacity-60">
        Lorem ipsum, dolor sit amet consectetur adipisicing elit. Commodi sint at atque illum esse
        accusantium necessitatibus, molestias fuga voluptatem voluptatibus provident ut maxime neque
        officia odit iusto eius illo nisi!
      </Paragraph>
      <RouterLink :to="{ name: ROUTE_CHARTER.name }">
        <Button>Notre charte</Button>
      </RouterLink>
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
import Paragraph from '@lychen/vue-components-website/paragraph/Paragraph.vue';
import persons from './data/team';
import Button from '@lychen/vue-components-core/button/Button.vue';
import { ROUTE_CHARTER } from '@/views/charter';
import Badge from '@lychen/vue-components-core/badge/Badge.vue';
import { EMAIL } from '@lychen/typescript-constants/Email';
import IconSendHorizontal from '@lychen/vue-icons/IconSendHorizontal.vue';
import IconCopy from '@lychen/vue-icons/IconCopy.vue';
import { useClipboard } from '@vueuse/core';
import { ref } from 'vue';
import CardPerson from '@/views/team/CardPerson.vue';
import Title from '@lychen/vue-components-website/title/Title.vue';

const { t } = usePrefixedI18n(CONFIG);

const contactEmail = ref(EMAIL.Contact);
const { copy, isSupported } = useClipboard({ source: contactEmail });
</script>
