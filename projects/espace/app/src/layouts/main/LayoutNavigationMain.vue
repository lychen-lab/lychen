<template>
  <nav class="bg-surface-container flex h-[70px] w-full flex-row justify-between px-6">
    <LayoutNavigationItem label="Accueil">
      <template #icon>
        <IconHouse />
      </template>
    </LayoutNavigationItem>
    <LayoutNavigationItem label="Terrains">
      <template #icon>
        <IconFence />
      </template>
    </LayoutNavigationItem>
    <LayoutNavigationItem label="Favoris">
      <template #icon>
        <IconHeartFilled />
      </template>
    </LayoutNavigationItem>
    <LayoutNavigationItem label="Personnes">
      <template #icon>
        <IconUsers />
      </template>
    </LayoutNavigationItem>
    <LayoutNavigationItem label="Chat">
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
</script>
