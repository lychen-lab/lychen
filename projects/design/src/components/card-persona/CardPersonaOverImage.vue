<template>
  <CardPersonaWrapper
    :persona-id="id"
    :html-id="htmlId"
    title="Design"
  >
    <div
      :id="htmlId"
      class="bg-surface-container-low group relative flex max-w-[300px] flex-col items-stretch justify-end rounded-2xl"
    >
      <img
        :src="`/persona/${id}.webp`"
        class="aspect-2/3 h-full rounded-2xl object-cover"
      />

      <div
        class="bg-surface/90 absolute right-2 bottom-2 left-2 flex flex-col items-stretch gap-2 rounded-xl p-4"
      >
        <div class="flex flex-row items-center justify-between">
          <BaseHeading variant="h2">{{ fullName }}</BaseHeading>
          <div class="flex flex-row gap-2">
            <Badge class="bg-tertiary-container text-on-tertiary-container">{{ id }}</Badge>
          </div>
        </div>

        <div class="flex basis-1/2 flex-col gap-2">
          <ul class="list-disc pl-4">
            <li
              v-for="(goal, index) in goals"
              :key="index"
              class="text-sm"
            >
              {{ goal }}
            </li>
          </ul>
        </div>
      </div>
    </div>
  </CardPersonaWrapper>
</template>

<script setup lang="ts">
import { type Props } from '.';
import BaseHeading from '@lychen/vue-components-app/base-heading/BaseHeading.vue';
import { Badge } from '@lychen/vue-components-core/badge';
import { useId } from 'vue';
import CardPersonaWrapper from './CardPersonaWrapper.vue';

const { id } = defineProps<Props>();

const htmlId = useId();
</script>

<style lang="css" scoped>
h5 {
  opacity: 0.6;
}
</style>
