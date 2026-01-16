<template>
  <div class="flex flex-col items-center justify-center">
    <slot name="icon" />
    <p class="text-[0.7rem] opacity-70">{{ label }}</p>
  </div>
</template>

<script lang="ts" setup>
defineProps<{
  label: string;
}>();
</script>
