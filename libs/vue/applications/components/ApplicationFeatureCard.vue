<template>
  <div class="flex flex-col rounded-xl p-2">
    <p class="text-sm font-bold">{{ title }}</p>
    <p class="text-sm opacity-80">{{ description }}</p>
  </div>
</template>

<script lang="ts" setup>
import { type ApplicationFeature } from '@lychen/typescript-applications/model/ApplicationFeature';

defineProps<ApplicationFeature>();
</script>
