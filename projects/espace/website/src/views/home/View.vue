<template>
  <Container class="flex flex-col items-center justify-center gap-4 py-20">
    <div
      class="relative flex flex-col items-center gap-8 rounded-2xl border-4 border-surface-container p-4 lg:flex-row"
    >
      <div class="absolute top-0 left-0 size-full overflow-hidden rounded-2xl">
        <img
          :src="heroImage"
          :alt="t('hero.image_alt')"
          class="size-full object-cover"
        />
      </div>
      <div class="z-10 flex flex-col items-center gap-4 lg:items-end">
        <div
          class="z-10 flex w-full flex-col items-start gap-4 rounded-2xl bg-surface-container/70 px-8 py-10 backdrop-blur-md lg:w-1/3"
        >
          <IconHeartHandshake class="size-18!" />
          <h2 class="text-on-white text-xl font-bold">
            {{ t('hero.description') }}
          </h2>
          <div class="flex flex-col items-start gap-2">
            <a
              :href="LINK.LandProposalForm"
              target="_blank"
            >
              <Button size="lg"
                >{{ t('hero.cta_share') }} <template #icon><IconArrowRight /></template
              ></Button>
            </a>
            <p class="text-xs opacity-80">
              {{ t('hero.email_prefix') }}
              <a
                href="mailto:contact@lychen.org"
                class="underline"
                >contact@lychen.org</a
              >
            </p>
          </div>
        </div>
      </div>
    </div>
  </Container>
  <Container class="flex flex-col items-start justify-center gap-8">
    <Title variant="h1">{{ t('search.title') }}</Title>
    <div class="flex flex-col items-start gap-2 md:flex-row md:items-center">
      <Paragraph>{{ t('search.subtitle') }}</Paragraph>
      <a
        :href="LINK.LandRequestForm"
        target="_blank"
      >
        <Button
          >{{ t('search.cta_form') }} <template #icon><IconArrowRight /></template
        ></Button>
      </a>
    </div>
  </Container>
  <Container class="flex flex-col items-start justify-center gap-8 text-left">
    <h2 class="text-[3rem]/[3rem] lg:text-[5rem]/[5rem]">
      {{ t('cta_bottom.line_1') }}
      <span
        class="bg-linear-to-r from-purple-500 via-orange-300 to-sky-500 bg-clip-text font-lexend font-extrabold text-transparent"
        >{{ t('cta_bottom.line_2') }}</span
      >
      {{ t('cta_bottom.line_3') }}
    </h2>
    <a
      :href="LINK.LandProposalForm"
      target="_blank"
    >
      <Button size="lg"
        >{{ t('cta_bottom.cta_share') }} <template #icon><IconArrowRight /></template
      ></Button>
    </a>
  </Container>
  <Container
    class="flex flex-col items-center justify-center gap-8 lg:grid lg:grid-cols-3 lg:flex-row lg:gap-8"
  >
    <div class="flex flex-col items-start justify-center gap-4">
      <Title variant="h2">{{ t('example.title') }}</Title>
      <Paragraph
        variant="website-default"
        class="opacity-80"
        >{{ t('example.paragraph') }}</Paragraph
      >
    </div>
    <div class="flex items-center justify-center lg:col-span-2">
      <HomeVideo
        video-id="t43-zErA2V8"
        :title="t('example.video_title')"
        :thumbnail="videoThumbnail"
        :play-label="t('example.video_play', { title: t('example.video_title') })"
        :notice="t('example.video_notice')"
      />
    </div>
  </Container>
</template>

<script lang="ts" setup>
import { usePrefixedI18n } from '@lychen/vue-i18n/composables/useI18nExtended';
import { Container } from '@lychen/vue-components-website/container';
import { Title } from '@lychen/vue-components-website/title';
import { Paragraph } from '@lychen/vue-components-website/paragraph';
import { Button } from '@lychen/vue-components-core/button';
import IconHeartHandshake from '@lychen/vue-icons/IconHeartHandshake.vue';
import IconArrowRight from '@lychen/vue-icons/IconArrowRight.vue';
import { CONFIG } from './i18n';
import HomeVideo from './HomeVideo.vue';
import heroImage from './assets/hero-market-garden.webp';
import videoThumbnail from './assets/video-gardening-differently.webp';
import { LINK } from '@lychen/typescript-espace/constants/App';
import { useExtendedHead } from '@lychen/vue-unhead-composables/useExtendedHead';

const { t } = usePrefixedI18n(CONFIG);

useExtendedHead(t, { ogImage: heroImage });
</script>

<style lang="css" scoped></style>
