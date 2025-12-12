<template>
  <Container class="flex flex-col items-center gap-4 pt-20">
    <Title variant="h1">{{ t('title') }}</Title>
    <Paragraph class="w-3/4 text-center opacity-60">{{ t('header_subtitle') }}</Paragraph>
    <div class="grid h-180 w-full grid-cols-4 grid-rows-3 gap-4 px-4 md:px-20">
      <div class="row-span-2 w-full overflow-hidden rounded-[100px] bg-teal-500">
        <img
          src="https://images.pexels.com/photos/3184431/pexels-photo-3184431.jpeg"
          class="h-full w-full object-cover"
        />
      </div>
      <div class="row-span-1 w-full overflow-hidden rounded-[100px] bg-amber-500">
        <img
          src="https://images.pexels.com/photos/1685650/pexels-photo-1685650.jpeg"
          class="h-full w-full object-cover"
        />
      </div>
      <div class="row-span-2 w-full overflow-hidden rounded-[100px] bg-orange-500">
        <img
          class="h-full w-full object-cover"
          src="https://images.pexels.com/photos/1172207/pexels-photo-1172207.jpeg"
        />
      </div>
      <div class="row-span-1 w-full overflow-hidden rounded-[100px] bg-purple-500">
        <img
          class="h-full w-full scale-110 object-cover hue-rotate-15"
          src="https://images.pexels.com/photos/774909/pexels-photo-774909.jpeg"
        />
      </div>
      <div class="row-span-2 w-full overflow-hidden rounded-[100px] bg-green-500">
        <img
          src="https://res.cloudinary.com/ddhvfiezg/image/upload/v1765545330/019b12b4-718c-7d11-b33e-dde66feeff83.webp"
          class="h-full w-full object-cover"
        />
      </div>
      <div class="row-span-2 w-full overflow-hidden rounded-[100px] bg-blue-500">
        <img
          src="https://images.pexels.com/photos/1685650/pexels-photo-1685650.jpeg"
          class="h-full w-full object-cover"
        />
      </div>
      <div class="row-span-1 w-full overflow-hidden rounded-[100px] bg-pink-500">
        <img
          src="https://images.pexels.com/photos/142497/pexels-photo-142497.jpeg"
          class="h-full w-full object-cover"
        />
      </div>
      <div class="row-span-2 w-full overflow-hidden rounded-[100px] bg-stone-500">
        <img
          src="https://images.pexels.com/photos/7583935/pexels-photo-7583935.jpeg"
          class="h-full w-full object-cover"
        />
      </div>
    </div>
  </Container>

  <Container class="flex flex-col items-center gap-4">
    <Title variant="h2">{{ t('team_section.title') }}</Title>
    <Paragraph class="text-center opacity-60 md:w-3/4">
      {{ t('team_section.description') }}
    </Paragraph>
    <div class="flex flex-row items-center gap-2">
      <small>{{ t('contact_label') }} </small>

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
    <div class="flex flex-col items-start justify-center gap-8">
      <Title variant="h2">{{ t('ecosystem_section.title') }}</Title>
      <Paragraph class="w-3/4 text-left opacity-60">
        {{ t('ecosystem_section.description') }}
      </Paragraph>
      <RouterLink :to="{ name: ROUTE_CHARTER.name }">
        <Button>{{ t('ecosystem_section.cta') }}</Button>
      </RouterLink>
    </div>
    <div class="overflow-hidden rounded-[100px]">
      <img
        src="https://res.cloudinary.com/ddhvfiezg/image/upload/v1765534714/ecosystem_y3ufwm.jpg"
        :alt="t('ecosystem_section.image_alt')"
        class="h-full w-full object-cover"
      />
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
