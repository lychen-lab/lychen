<template>
  <div class="flex flex-col gap-4">
    <div class="flex flex-col items-stretch justify-between gap-4 lg:flex-row">
      <div class="flex basis-1/3 flex-col gap-2">
        <slot name="logo"><LogoLychenFull class="h-14" /></slot>
        <p
          v-if="seoParagraph"
          class="text-sm opacity-80"
        >
          {{ seoParagraph }}
        </p>
        <p
          v-if="displayPronunciation"
          class="text-sm opacity-60"
        >
          {{ t(`pronounce`) }}
        </p>
      </div>

      <div class="flex basis-1/4 flex-col items-end justify-center gap-2 text-sm">
        <div class="flex flex-row gap-4">
          <SelectLanguage />
          <ToggleColorScheme />
        </div>
        <p class="font-medium">{{ EMAIL.Contact }}</p>
      </div>
    </div>

    <div class="flex flex-col-reverse items-center justify-between gap-4 lg:flex-row">
      <small class="text-xs"
        >{{ t(`copyright`, { date: year }) }} - SIRET {{ INFORMATION.Siret }}</small
      >
      <div class="flex flex-row gap-2 text-xs opacity-60">
        <RouterLink
          v-for="(menu, _index) in legalMenus"
          :key="_index"
          :to="i18nRoute(menu.to)"
        >
          {{ menu.title }}
        </RouterLink>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { defineAsyncComponent } from 'vue';

import { CONFIG } from './i18n';
import { usePrefixedI18n } from '@lychen/vue-i18n/composables/useI18nExtended';
import { EMAIL } from '@lychen/typescript-constants/Email';
import { INFORMATION } from '@lychen/typescript-constants/Information';
import SelectLanguage from '@lychen/vue-i18n/components/select-language/SelectLanguage.vue';
import ToggleColorScheme from '@lychen/vue-color-scheme/components/ToggleColorScheme.vue';

const LogoLychenFull = defineAsyncComponent(
  () => import('@lychen/vue-components-extra/logo-lychen/LogoLychenFull.vue'),
);

const { t, i18nRoute } = usePrefixedI18n(CONFIG);

interface Props {
  legalMenus?: { title: string; to: unknown }[];
  seoParagraph?: string;
  displayPronunciation?: boolean;
}

const { displayPronunciation = false } = defineProps<Props>();

const year = new Date().getFullYear();
</script>
