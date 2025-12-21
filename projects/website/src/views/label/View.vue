<template>
  <Container class="flex flex-col gap-16 pt-20">
    <!-- Hero Section -->
    <div class="mx-auto flex max-w-4xl flex-col items-center gap-6 text-center">
      <Title
        variant="h1"
        class="text-balance"
        >{{ t('hero.title') }}</Title
      >
      <Paragraph class="text-lg opacity-80">{{ t('hero.description') }}</Paragraph>
    </div>
  </Container>

  <Container>
    <!-- The Scale Section -->
    <div class="flex flex-col gap-8">
      <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
        <!-- Transition (1-3) -->
        <div
          class="bg-surface-container/50 border-surface-outline flex flex-col gap-4 rounded-3xl border p-6"
        >
          <div class="mb-4 flex flex-col items-center gap-2">
            <Title
              variant="h3"
              class="text-yellow-600 dark:text-yellow-400"
              >{{ t('tranches.transition.title') }}</Title
            >
            <span class="text-sm opacity-60">{{ t('tranches.transition.subtitle') }}</span>
          </div>
          <div class="flex flex-wrap justify-center gap-4">
            <RobustLabel :level="1" />
            <RobustLabel :level="2" />
            <RobustLabel :level="3" />
          </div>
          <Paragraph class="mt-4 text-center text-sm">{{
            t('tranches.transition.description')
          }}</Paragraph>
        </div>

        <!-- Preservation (4-7) -->
        <div
          class="bg-surface-container/50 border-surface-outline flex flex-col gap-4 rounded-3xl border p-6"
        >
          <div class="mb-4 flex flex-col items-center gap-2">
            <Title
              variant="h3"
              class="text-emerald-600 dark:text-emerald-400"
              >{{ t('tranches.preservation.title') }}</Title
            >
            <span class="text-sm opacity-60">{{ t('tranches.preservation.subtitle') }}</span>
          </div>
          <div class="flex flex-wrap justify-center gap-4">
            <RobustLabel :level="4" />
            <RobustLabel :level="5" />
            <RobustLabel :level="6" />
            <RobustLabel :level="7" />
          </div>
          <Paragraph class="mt-4 text-center text-sm">{{
            t('tranches.preservation.description')
          }}</Paragraph>
        </div>

        <!-- Regeneration (8-10) -->
        <div
          class="bg-surface-container/50 border-surface-outline flex flex-col gap-4 rounded-3xl border shadow-lg shadow-amber-500/10"
        >
          <div class="mb-4 flex flex-col items-center gap-2">
            <Title
              variant="h3"
              class="text-amber-600 dark:text-amber-400"
              >{{ t('tranches.regeneration.title') }}</Title
            >
            <span class="text-sm opacity-60">{{ t('tranches.regeneration.subtitle') }}</span>
          </div>
          <div class="flex flex-wrap justify-center gap-4">
            <RobustLabel :level="8" />
            <RobustLabel :level="9" />
            <RobustLabel :level="10" />
          </div>
          <Paragraph class="mt-4 text-center text-sm">{{
            t('tranches.regeneration.description')
          }}</Paragraph>
        </div>
      </div>
    </div>
  </Container>

  <Container>
    <!-- Gamification / Context -->
    <div class="grid items-center gap-12 md:grid-cols-2">
      <div class="flex flex-col gap-6">
        <Title variant="h2">{{ t('gamification.title') }}</Title>
        <Paragraph>{{ t('gamification.description') }}</Paragraph>
        <ul class="flex flex-col gap-4">
          <li
            v-for="(_, i) in 4"
            :key="i"
            class="bg-surface-container flex items-center gap-4 rounded-xl p-3"
          >
            <RobustLabel
              :level="[6, 4, 5, 8][i] ?? 1"
              size="sm"
              class="scale-75"
            />
            <div class="flex flex-col">
              <span class="font-bold">{{ t(`gamification.examples.${i}.role`) }}</span>
              <span class="text-sm opacity-60">{{ t(`gamification.examples.${i}.points`) }}</span>
            </div>
          </li>
        </ul>
      </div>
      <div class="flex flex-col gap-6">
        <Title variant="h2">{{ t('vision.title') }}</Title>
        <Paragraph>{{ t('vision.description') }}</Paragraph>
      </div>
    </div>
  </Container>
</template>

<script lang="ts" setup>
import { usePrefixedI18n } from '@lychen/vue-i18n/composables/useI18nExtended';
import { CONFIG } from './i18n';
import Container from '@lychen/vue-components-website/container/Container.vue';
import Title from '@lychen/vue-components-website/title/Title.vue';
import Paragraph from '@lychen/vue-components-website/paragraph/Paragraph.vue';
import RobustLabel from './RobustLabel.vue';
import { useExtendedHead } from '@lychen/vue-unhead-composables/useExtendedHead';

const { t } = usePrefixedI18n(CONFIG);
useExtendedHead(t);
</script>
