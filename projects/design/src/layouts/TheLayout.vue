<template>
  <LayoutInAppRoot>
    <LayoutInAppSideNavigation class="flex flex-col justify-between">
      <LayoutInAppSideNavigationHeader class="flex flex-row items-center gap-2">
        <LogoLychenIconOnly class="h-8 w-8" />
        <div class="flex flex-col">
          <span class="font-lexend text-lg font-bold">Design</span>
          <span class="-mt-1 text-sm opacity-60">by Lychen</span>
        </div>
      </LayoutInAppSideNavigationHeader>
      <div class="flex flex-col gap-4">
        <LayoutInAppSideNavigationSection
          v-for="(section, indexSections) in navigation"
          :key="indexSections"
          :title="section.title"
        >
          <template v-if="section.items">
            <LayoutInAppSideNavigationItem
              v-for="(item, indexItems) in section.items"
              :key="indexItems"
              v-bind="item"
            />
          </template>
        </LayoutInAppSideNavigationSection>
      </div>
      <LayoutInAppSideNavigationFooter>
        <a href="https://lychen.org">lychen.org <IconArrowUpRight class="text-xs opacity-80" /></a>
      </LayoutInAppSideNavigationFooter>
    </LayoutInAppSideNavigation>
    <LayoutInAppContent />
    <LayoutInAppHeader>
      <div class="flex w-full flex-row justify-end gap-4 p-4">
        <ToggleColorScheme />
        <SelectLanguage />
      </div>
    </LayoutInAppHeader>
  </LayoutInAppRoot>
</template>

<script lang="ts" setup>
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';

import { RoutePageColors } from '@/pages/colors';
import { RoutePagePersonas } from '@/pages/personas';
import { TRANSLATION_KEY, messages } from './i18n';
import { RoutePageHome } from '@/pages/home';
import ToggleColorScheme from '@lychen/vue-color-scheme/components/ToggleColorScheme.vue';
import SelectLanguage from '@lychen/vue-i18n/components/select-language/SelectLanguage.vue';
import { computed } from 'vue';
import LayoutInAppRoot from './in-app/LayoutInAppRoot.vue';
import LayoutInAppSideNavigation from './in-app/LayoutInAppSideNavigation.vue';
import LayoutInAppSideNavigationItem from './in-app/LayoutInAppSideNavigationItem.vue';
import LayoutInAppSideNavigationSection from './in-app/LayoutInAppSideNavigationSection.vue';
import LayoutInAppHeader from './in-app/LayoutInAppHeader.vue';
import LayoutInAppContent from './in-app/LayoutInAppContent.vue';
import LayoutInAppSideNavigationHeader from './in-app/LayoutInAppSideNavigationHeader.vue';
import LayoutInAppSideNavigationFooter from './in-app/LayoutInAppSideNavigationFooter.vue';

import { LogoLychenIconOnly } from '@lychen/vue-components-extra/logo-lychen';
import IconArrowUpRight from '@lychen/vue-icons/IconArrowUpRight.vue';

const { t } = useI18nExtended({ messages, rootKey: TRANSLATION_KEY, prefixed: true });

const navigation = computed(() => {
  return [
    {
      items: [
        {
          label: t('menu.home'),
          to: RoutePageHome,
        },
      ],
    },
    {
      title: t('section.brand'),
      items: [
        {
          label: t('menu.personas'),
          to: RoutePagePersonas,
        },
      ],
    },
    {
      title: t('section.foundations'),
      items: [
        {
          label: t('menu.colors'),
          to: RoutePageColors,
        },
      ],
    },
  ];
});
</script>

<style scoped></style>
