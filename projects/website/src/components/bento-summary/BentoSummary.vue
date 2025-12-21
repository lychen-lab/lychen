<template>
  <div class="bento-grid mt-8 self-stretch">
    <div class="item justify-center">
      <IconEarth class="text-5xl" />
      <p class="text-xl">{{ t('integrability.title') }}</p>
    </div>
    <div class="item justify-between">
      {{ t('objective.title') }}
      <p class="gradient text-5xl">{{ t('objective.year') }}</p>
      {{ t('objective.version') }}
    </div>
    <div class="item justify-between">
      <p>{{ t('local_value.title') }}</p>
      <p class="gradient text-4xl">{{ t('local_value.keyword') }}</p>
      <p>{{ t('local_value.subtitle') }}</p>
    </div>
    <div class="item justify-center">
      <p class="gradient text-xl md:text-2xl">{{ t('data_ownership.title') }}</p>
    </div>
    <div class="item justify-center">
      <p class="gradient text-4xl">{{ t('accessibility.title') }}</p>
      <p>{{ t('accessibility.subtitle') }}</p>
    </div>
    <div class="item justify-center">
      <p class="gradient text-xl md:text-2xl">{{ t('citizen_change.title') }}</p>
    </div>
    <div class="item bg-surface-container-high">
      <img
        :src="bentoCentralImageUrl"
        class="w-2/3 rounded-xl"
        alt="number 10 as organic blue and green shapes"
      />
      <p class="gradient text-4xl">{{ t('applications.title') }}</p>
      <p>{{ t('applications.subtitle') }}</p>
    </div>
    <div class="item justify-between">
      {{ t('sustainable_development.title') }} <br />
      <p class="gradient text-8xl">{{ t('sustainable_development.sdg_number') }}</p>
      {{ t('sustainable_development.subtitle') }}
    </div>
    <div class="item justify-between">
      <div></div>
      <div>
        <IconHeartHandshake class="text-5xl" />
        <p class="gradient text-2xl md:text-4xl">{{ t('sponsorship.title') }}</p>
      </div>
      {{ t('sponsorship.description') }}
    </div>
    <div class="item justify-center">
      <p class="gradient text-2xl">{{ t('ethics_inclusion.title') }}</p>
    </div>
    <div class="item justify-center">
      <p class="gradient text-5xl">{{ t('compromise.title') }}</p>
      <p>{{ t('compromise.subtitle') }}</p>
    </div>
    <div class="item justify-center">
      <p class="gradient text-xl md:text-2xl">{{ t('communication.title') }}</p>
    </div>
    <div class="item justify-center">
      <p class="text-2xl">{{ t('team.title') }}</p>
    </div>
    <div class="item bg-surface-container text-on-surface-container justify-center">
      {{ t('transparency.title') }}
      <p class="gradient text-2xl">
        {{ t('transparency.subtitle') }}
      </p>
    </div>
  </div>
</template>

<script setup lang="ts">
import bentoCentralImageUrl from './assets/bento-central.webp';
import { defineAsyncComponent } from 'vue';
import { CONFIG } from './i18n';
import { usePrefixedI18n } from '@lychen/vue-i18n/composables/useI18nExtended';
import IconHeartHandshake from '@lychen/vue-icons/IconHeartHandshake.vue';
import IconEarth from '@lychen/vue-icons/IconEarth.vue';

const { t } = usePrefixedI18n(CONFIG);
</script>

<style style="css" scoped>
@layer components {
  .bento-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    grid-template-rows: repeat(6, auto);
    gap: calc(var(--spacing) * 4);
    grid-template-areas:
      'one two three four four five five'
      'six six seven seven seven eight nine'
      'six six seven seven seven eight nine'
      'ten eleven seven seven seven eight nine'
      'ten eleven seven seven seven twelve twelve'
      'thirteen thirteen fourteen fourteen fourteen twelve twelve';

    @media not all and (min-width: 1024px) {
      & {
        grid-template-columns: repeat(4, 1fr);
        grid-template-rows: auto;
        grid-template-areas:
          'seven seven seven one'
          'seven seven seven two'
          'three four five five'
          'six eight nine ten'
          'six eight nine eleven'
          'twelve twelve thirteen thirteen'
          'fourteen fourteen fourteen fourteen';
      }
    }

    @media not all and (min-width: 640px) {
      & {
        grid-template-columns: repeat(2, 1fr);
        grid-template-rows: auto;
        grid-template-areas:
          'seven seven'
          'one two'
          'three two'
          'three four'
          'three four'
          'five five'
          'eight six'
          'eight nine'
          'ten nine'
          'ten eleven'
          'twelve thirteen'
          'fourteen fourteen';
      }
    }

    .item {
      display: flex;
      flex-flow: column;
      align-items: center;
      padding: calc(var(--spacing) * 6) calc(var(--spacing) * 4);
      border-radius: var(--radius-3xl);
      background: var(--color-surface-container);
      color: var(--color-on-surface-container);
      font-weight: var(--font-weight-bold);
      text-align: center;
      gap: calc(var(--spacing) * 4);

      p.gradient {
        background: var(--linear-gradient-primary-to-tertiary);
        background-clip: text;
        -webkit-text-fill-color: transparent;
      }

      &:nth-child(1) {
        grid-area: one;
      }

      &:nth-child(2) {
        grid-area: two;
      }

      &:nth-child(3) {
        grid-area: three;
      }

      &:nth-child(4) {
        grid-area: four;
      }

      &:nth-child(5) {
        grid-area: five;
      }

      &:nth-child(6) {
        grid-area: six;
      }
      &:nth-child(7) {
        grid-area: seven;
      }
      &:nth-child(8) {
        grid-area: eight;
      }
      &:nth-child(9) {
        grid-area: nine;
      }
      &:nth-child(10) {
        grid-area: ten;
      }
      &:nth-child(11) {
        grid-area: eleven;
      }
      &:nth-child(12) {
        grid-area: twelve;
      }
      &:nth-child(13) {
        grid-area: thirteen;
      }
      &:nth-child(14) {
        grid-area: fourteen;
      }
    }
  }
}
</style>
