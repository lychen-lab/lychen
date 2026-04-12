<template>
  <nav class="bg-surface-container flex h-[70px] w-full flex-row justify-between px-6">
    <LayoutNavigationItem
      label="Cultures"
      :route-name="ROUTE_CULTURES.name as string"
    >
      <template #icon>
        <IconList />
      </template>
    </LayoutNavigationItem>
    <LayoutNavigationItem
      label="Tasks"
      :route-name="ROUTE_CULTURES.name as string"
    >
      <template #icon>
        <IconCalendarCirclePlus />
      </template>
    </LayoutNavigationItem>
    <LayoutNavigationItem
      label="Forecast"
      :route-name="ROUTE_CULTURES.name as string"
    >
      <template #icon>
        <IconEarth />
      </template>
    </LayoutNavigationItem>
  </nav>
</template>

<script lang="ts" setup>
import LayoutNavigationItem from '@/layouts/main/LayoutNavigationItem.vue';
import IconList from '@lychen/vue-icons/IconList.vue';
import IconCalendarCirclePlus from '@lychen/vue-icons/IconCalendarCirclePlus.vue';
import IconEarth from '@lychen/vue-icons/IconEarth.vue';
import { ROUTE_CULTURES } from '@/views/cultures';
</script>
