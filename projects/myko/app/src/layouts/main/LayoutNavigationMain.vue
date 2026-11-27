<template>
  <nav
    :aria-label="t('navigation')"
    class="box-content flex h-16 w-full flex-row bg-surface-container px-2 pb-[env(safe-area-inset-bottom)]"
  >
    <LayoutNavigationItem
      v-for="item in items"
      :key="item.routeName"
      :label="item.label"
      :route-name="item.routeName"
      :active-paths="item.activePaths"
    >
      <template #icon>
        <component :is="item.icon" />
      </template>
    </LayoutNavigationItem>
  </nav>
</template>

<script lang="ts" setup>
import { computed } from 'vue';

import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import IconCamera from '@lychen/vue-icons/IconCamera.vue';
import IconEllipsis from '@lychen/vue-icons/IconEllipsis.vue';
import IconHouse from '@lychen/vue-icons/IconHouse.vue';
import IconKanban from '@lychen/vue-icons/IconKanban.vue';
import IconSprout from '@lychen/vue-icons/IconSprout.vue';

import { ROUTE_NAME } from '@/router/routeNames';

import { MESSAGES, TRANSLATION_KEY } from './i18n';
import LayoutNavigationItem from './LayoutNavigationItem.vue';

const { t } = useI18nExtended({ messages: MESSAGES, rootKey: TRANSLATION_KEY, prefixed: true });

// Accueil, Bûches, Scanner, Stats, Plus (zones, souches, étiquettes): the spec's bottom navigation.
const items = computed(() => [
  { routeName: ROUTE_NAME.Home, label: t('home'), icon: IconHouse, activePaths: ['/'] },
  { routeName: ROUTE_NAME.Logs, label: t('logs'), icon: IconSprout, activePaths: ['/logs'] },
  { routeName: ROUTE_NAME.Scan, label: t('scan'), icon: IconCamera, activePaths: ['/scan'] },
  { routeName: ROUTE_NAME.Stats, label: t('stats'), icon: IconKanban, activePaths: ['/stats'] },
  {
    routeName: ROUTE_NAME.More,
    label: t('more'),
    icon: IconEllipsis,
    activePaths: ['/more', '/zones', '/strains', '/labels'],
  },
]);
</script>
