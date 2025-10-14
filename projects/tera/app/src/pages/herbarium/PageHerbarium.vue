<template>
  <SectionWithTitle :title="t('title')">
    <DivWithBackgroundImg
      :background-image="bannerImg"
      overlay
      overlay-class="bg-gradient-to-tr from-tertiary-container to-tertiary-container/60"
      class="text-on-tertiary-container flex flex-col gap-4 rounded-xl p-8 lg:py-20"
    >
      <BaseHeading
        variant="h2"
        class="text-on-tertiary-container z-10"
        >{{ t('banner.title') }}</BaseHeading
      >
      <p class="z-10 lg:w-2/3">
        {{ t('banner.description') }}
      </p>
    </DivWithBackgroundImg>
    <div>
      <BaseHeading variant="h2">
        {{ t('global.title') }}
      </BaseHeading>
      <p>{{ t('global.description') }}</p>
    </div>
    <div>
      <BaseHeading variant="h2">
        {{ t('custom.title') }}
      </BaseHeading>
      <p>{{ t('custom.description') }}</p>
    </div>
    <SectionDevelopmentInProgress />
  </SectionWithTitle>
</template>

<script lang="ts" setup>
import bannerImg from './assets/banner.webp';
import SectionWithTitle from '@lychen/vue-components-app/section-with-title/SectionWithTitle.vue';
import { messages, TRANSLATION_KEY } from './i18n';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import { BaseHeading } from '@lychen/vue-components-app/base-heading';
import { defineAsyncComponent } from 'vue';
import SectionDevelopmentInProgress from '@lychen/vue-components-app/section-development-in-progress/SectionDevelopmentInProgress.vue';

const DivWithBackgroundImg = defineAsyncComponent(
  () => import('@lychen/vue-components-extra/div-with-background-img/DivWithBackgroundImg.vue'),
);

const { t } = useI18nExtended({ messages, rootKey: TRANSLATION_KEY, prefixed: true });
</script>

<style lang="css" scoped></style>
