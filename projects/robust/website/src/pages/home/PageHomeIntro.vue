<template>
  <Container class="flex flex-col items-center gap-8 xl:mb-20 xl:flex-row">
    <div class="flex flex-col gap-6 md:basis-1/3">
      <Title class="text-5xl md:text-8xl"
        >{{ t('intro.title.1') }} <br />
        {{ t('intro.title.2') }} <br />

        <TextHighlight
          class="rounded-lg bg-gradient-to-r from-indigo-300 to-purple-300 md:text-8xl"
          text-end-color="var(--color-surface)"
          >{{ t('intro.title.3') }}</TextHighlight
        ></Title
      >
      <Title
        variant="h3"
        class="opacity-80"
        >{{ t('intro.sub_title') }}</Title
      >
      <Paragraph variant="website-highlight">{{ t('intro.highlight_text') }}</Paragraph>
    </div>

    <div class="relative flex basis-2/3 flex-col items-center gap-8 rounded-2xl">
      <img
        :src="ImageUrl"
        class="rounded-3xl"
      />
      <div
        class="z-30 -mt-40 flex w-8/10 flex-col items-center gap-4 md:absolute md:w-auto lg:-bottom-1/3 lg:left-1/5 xl:-bottom-3/5 2xl:-bottom-2/5"
      >
        <div
          class="bg-surface-container/50 text-on-surface-container flex flex-col items-center rounded-xl px-4 py-2 backdrop-blur-lg"
        >
          <p class="text-md font-medium">{{ t('intro.inspire_others') }}</p>
          <IconChevronDown />
        </div>
        <div
          class="bg-surface-container/80 shadow-primary/50 dark:shadow-primary-container/50 outline-surface/50 flex flex-col gap-4 self-stretch rounded-xl p-6 shadow-2xl outline outline-offset-4 backdrop-blur-lg md:w-[300px] md:self-center"
        >
          <div class="flex flex-row items-center justify-between gap-6">
            <img
              :src="DudeImageUrl"
              class="h-8 rounded-full"
            />
            <p class="text-md font-bold md:text-xl">Kyan Mateouf</p>
          </div>
          <div class="flex flex-row items-center justify-center gap-4">
            <img
              :src="RewardsImageUrl"
              class="h-6"
            />
            <img
              :src="AwardImageUrl"
              class="fa-beat h-8"
            />
            <img
              :src="BadgeImageUrl"
              class="h-6"
            />
          </div>
          <div class="flex flex-col gap-2">
            <div class="h-3 w-full rounded-md bg-gray-300"></div>
            <div class="h-3 w-full rounded-md bg-gray-300"></div>
            <div class="h-3 w-1/2 rounded-md bg-gray-300"></div>
          </div>
          <p class="text-sm">{{ t('intro.actions') }}</p>
          <div class="flex flex-col gap-2">
            <div class="h-3 w-full rounded-md bg-gray-300"></div>
            <div class="h-3 w-full rounded-md bg-gray-300"></div>
            <div class="h-3 w-full rounded-md bg-gray-300"></div>
          </div>
          <p class="text-sm">{{ t('intro.mesh') }}</p>
          <div class="grid grid-cols-2 gap-2">
            <div class="h-10 w-full rounded-md bg-gray-300"></div>
            <div class="h-10 w-full rounded-md bg-gray-300"></div>
            <div class="h-10 w-full rounded-md bg-gray-300"></div>
            <div class="h-10 w-full rounded-md bg-gray-300"></div>
          </div>
        </div>
      </div>

      <div
        class="flex flex-col items-center gap-4 md:absolute md:w-2/3 md:flex-row lg:top-8 lg:-right-8"
      >
        <div
          class="bg-surface-container/50 text-on-surface-container flex flex-row items-center rounded-xl px-4 py-2 backdrop-blur-lg"
        >
          <p class="text-md font-medium">{{ t('intro.win_success') }}</p>

          <IconChevronRight class="w-8" />
        </div>
        <div
          class="bg-surface-container/80 shadow-primary/50 dark:shadow-primary-container/50 flex flex-col items-center justify-center gap-4 rounded-md p-6 shadow-2xl backdrop-blur-lg md:flex-row"
        >
          <img
            :src="AwardImageUrl"
            class="w-20"
          />
          <div class="text-center md:text-left">
            <p class="text-lg font-bold">{{ t('intro.fake_success.title') }}</p>
            <p>
              {{ t('intro.fake_success.description') }}
            </p>
          </div>
        </div>
      </div>

      <div
        class="bg-surface-container/80 shadow-primary/50 dark:shadow-primary-container/50 hidden flex-col items-center justify-center gap-2 rounded-xl p-6 shadow-xl backdrop-blur-lg md:absolute md:flex lg:right-10 lg:bottom-20"
      >
        <img
          :src="CompanyImageUrl"
          class="bg-on-surface h-16 rounded-full"
        />
        <p class="font-lexend text-lg font-bold">alpsify</p>
        <div class="flex flex-row items-center justify-center gap-1 text-orange-400">
          <IconStar />
          <IconStar />
          <IconStar />
        </div>
        <div class="flex flex-col items-center justify-center gap-2 self-stretch">
          <div class="h-3 w-full rounded-md bg-gray-300"></div>
          <div class="h-3 w-full rounded-md bg-gray-300"></div>
          <div class="h-3 w-1/2 rounded-md bg-gray-300"></div>
        </div>
      </div>
    </div>
  </Container>
</template>

<script lang="ts" setup>
import ImageUrl from './assets/WomanJumping.webp';
import DudeImageUrl from './assets/dude.jpg';
import AwardImageUrl from './assets/award.png';
import RewardsImageUrl from './assets/rewards.png';
import BadgeImageUrl from './assets/badge.png';
import CompanyImageUrl from './assets/CompanyLogo.png';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';

import { messages, TRANSLATION_KEY } from './i18n';
import Container from '@lychen/vue-components-website/container/Container.vue';
import Title from '@lychen/vue-components-website/title/Title.vue';
import Paragraph from '@lychen/vue-components-website/paragraph/Paragraph.vue';
import { TextHighlight } from '@lychen/vue-components-extra/text-highlight';

import IconChevronDown from '@lychen/vue-icons/IconChevronDown.vue';
import IconChevronRight from '@lychen/vue-icons/IconChevronRight.vue';
import IconStar from '@lychen/vue-icons/IconStar.vue';

const { t } = useI18nExtended({ messages, rootKey: TRANSLATION_KEY, prefixed: true });
</script>

<style lang="css" scoped></style>
