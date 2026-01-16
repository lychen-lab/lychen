<template>
  <RouterLink
    :to="{ name: routeName }"
    class="flex flex-col items-center justify-center"
  >
    <slot name="icon" />
    <p class="text-[0.7rem] opacity-60">{{ label }}</p>
  </RouterLink>
</template>

<script lang="ts" setup>
defineProps<{
  label: string;
  routeName: string;
}>();
</script>
