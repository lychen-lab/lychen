<template>
  <Container class="flex flex-col items-center gap-8 pt-20 text-center">
    <Title variant="h1">{{ t('title') }}</Title>
    <Paragraph class="">{{ t('description') }}</Paragraph>
  </Container>
  <Container class="flex flex-col items-center gap-8 text-center">
    <Title variant="h2">{{ t('current.title') }}</Title>
    <Paragraph>{{ t('current.description') }}</Paragraph>
    <div class="flex flex-row flex-wrap items-center gap-8">
      <PartnerCard
        :link="LINK.Alpsify"
        name="Alpsify"
        image="https://res.cloudinary.com/ddhvfiezg/image/upload/v1766332793/019b41a3-c25f-7ad3-960d-76c8a0b45104.webp"
        :description="t('alpsify.description')"
      />
      <PartnerCard
        :link="LINK.HumusAndCo"
        name="Humus & Co"
        image="https://res.cloudinary.com/ddhvfiezg/image/upload/v1766333076/019b41a8-11e5-7729-b13e-cc0c238c80f8.webp"
        :description="t('humus_and_co.description')"
      />
    </div>
  </Container>
  <Container class="flex flex-col gap-8 md:grid md:grid-cols-2">
    <div class="flex flex-col items-start justify-center gap-8">
      <Title variant="h2">{{ t('become_section.title') }}</Title>
      <Paragraph class="w-3/4 text-left opacity-60">
        {{ t('become_section.description') }}
      </Paragraph>
      <RouterLink :to="{ name: ROUTE_TEAM.name }">
        <Button>{{ t('become_section.cta') }}</Button>
      </RouterLink>
    </div>
    <div class="overflow-hidden rounded-[100px]">
      <img
        src="https://res.cloudinary.com/ddhvfiezg/image/upload/v1766332300/019b419c-380d-7968-86d2-86ce334f7e67.webp"
        :alt="t('become_section.image_alt')"
        class="h-full w-full object-cover"
        loading="lazy"
        decoding="async"
      />
    </div>
  </Container>
</template>

<script lang="ts" setup>
import { usePrefixedI18n } from '@lychen/vue-i18n/composables/useI18nExtended';
import { CONFIG } from './i18n';
import Container from '@lychen/vue-components-website/container/Container.vue';
import Title from '@lychen/vue-components-website/title/Title.vue';
import Paragraph from '@lychen/vue-components-website/paragraph/Paragraph.vue';
import { ROUTE_TEAM } from '@/views/team';
import Button from '@lychen/vue-components-core/button/Button.vue';
import { LINK } from '@lychen/typescript-constants/Link';
import PartnerCard from '@/views/partnerships/PartnerCard.vue';
import { useExtendedHead } from '@lychen/vue-unhead-composables/useExtendedHead';

const { t } = usePrefixedI18n(CONFIG);
useExtendedHead(t);
</script>
