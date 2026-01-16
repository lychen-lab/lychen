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
        <IconHeartFilled />
      </template>
    </LayoutNavigationItem>
    <LayoutNavigationItem
      label="Personnes"
      :route-name="ROUTE_LAND_REQUESTS.name"
    >
      <template #icon>
        <IconUsers />
      </template>
    </LayoutNavigationItem>
    <LayoutNavigationItem
      label="Chats"
      :route-name="ROUTE_CHATS.name"
    >
      <template #icon>
        <IconMessagesSquare />
      </template>
    </LayoutNavigationItem>
  </nav>
</template>

<script lang="ts" setup>
import LayoutNavigationItem from '@/layouts/main/LayoutNavigationItem.vue';
import IconHeartFilled from '@lychen/vue-icons/IconHeartFilled.vue';
import IconHouse from '@lychen/vue-icons/IconHouse.vue';
import IconMessagesSquare from '@lychen/vue-icons/IconMessagesSquare.vue';
import IconFence from '@lychen/vue-icons/IconFence.vue';
import IconUsers from '@lychen/vue-icons/IconUsers.vue';
import { ROUTE_HOME } from '@/views/home';
import { ROUTE_LAND_PROPOSALS } from '@/views/land-proposals';
import { ROUTE_LAND_REQUESTS } from '@/views/land-requests';
import { ROUTE_FAVORITES } from '@/views/favorites';
import { ROUTE_CHATS } from '@/views/chats';
</script>
