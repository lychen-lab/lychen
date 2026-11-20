<template>
  <nav class="flex h-[70px] w-full flex-row justify-between bg-surface-container px-6">
    <LayoutNavigationItem
      :label="t('navigation.home')"
      :route-name="ROUTE_HOME.name"
    >
      <template #icon>
        <IconHouse />
      </template>
    </LayoutNavigationItem>
    <LayoutNavigationItem
      :label="t('navigation.proposals')"
      :route-name="ROUTE_LAND_PROPOSALS.name"
    >
      <template #icon>
        <IconFence />
      </template>
    </LayoutNavigationItem>
    <LayoutNavigationItem
      :label="t('navigation.requests')"
      :route-name="ROUTE_LAND_REQUESTS.name"
    >
      <template #icon>
        <IconSearch />
      </template>
    </LayoutNavigationItem>
    <LayoutNavigationItem
      :label="t('navigation.favorites')"
      :route-name="ROUTE_FAVORITES.name"
    >
      <template #icon>
        <IconHeart />
      </template>
    </LayoutNavigationItem>
  </nav>
</template>

<script lang="ts" setup>
import LayoutNavigationItem from '@/layouts/main/LayoutNavigationItem.vue';
import IconHouse from '@lychen/vue-icons/IconHouse.vue';
import IconFence from '@lychen/vue-icons/IconFence.vue';
import IconHeart from '@lychen/vue-icons/IconHeart.vue';
import IconSearch from '@lychen/vue-icons/IconSearch.vue';
import { useAppI18n } from '@/i18n';
import { ROUTE_HOME } from '@/views/home';
import { ROUTE_LAND_PROPOSALS } from '@/views/land-proposals';
import { ROUTE_LAND_REQUESTS } from '@/views/land-requests';
import { ROUTE_FAVORITES } from '@/views/favorites';

const { t } = useAppI18n();
</script>
