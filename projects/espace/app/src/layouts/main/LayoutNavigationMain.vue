<template>
  <nav class="bg-surface-container flex h-[70px] w-full flex-row justify-between px-6">
    <LayoutNavigationItem
      label="Accueil"
      :route-name="ROUTE_HOME.name"
    >
      <template #icon>
        <IconHouse />
      </template>
    </LayoutNavigationItem>
    <LayoutNavigationItem
      label="Terrains"
      :route-name="ROUTE_LAND_PROPOSALS.name"
    >
      <template #icon>
        <IconFence />
      </template>
    </LayoutNavigationItem>
    <LayoutNavigationItem
      label="Favoris"
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
import { ROUTE_HOME } from '@/views/home';
import { ROUTE_LAND_PROPOSALS } from '@/views/land-proposals';
import { ROUTE_FAVORITES } from '@/views/favorites';
</script>
