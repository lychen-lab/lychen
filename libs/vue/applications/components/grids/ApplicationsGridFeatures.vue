<template>
  <div class="grid-rows-auto grid grid-cols-1 gap-10 md:grid-cols-(--grid-fluid) md:grid-rows-1">
    <div
      v-for="(group, index) in features"
      :key="index"
    >
      <p class="text-xl uppercase opacity-60">{{ group.title }}</p>
      <ApplicationFeatureCard
        v-for="feature in group.features"
        v-bind="feature"
        :key="feature.alias"
        class="pl-0"
      />
    </div>
  </div>
</template>

<script setup lang="ts">
import { type OrganizedFeaturesByGroup } from '@lychen/typescript-applications/model/OrganizedFeaturesByGroup';
import ApplicationFeatureCard from '../ApplicationFeatureCard.vue';

defineProps<{ features: OrganizedFeaturesByGroup }>();
</script>
