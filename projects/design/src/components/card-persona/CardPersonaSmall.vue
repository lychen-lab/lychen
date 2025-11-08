<template>
  <CardPersonaWrapper
    :persona-id="id"
    :html-id="htmlId"
    title="Small"
  >
    <div
      :id="htmlId"
      class="bg-surface-container-low flex max-h-20 flex-row items-center gap-4 rounded-xl p-4"
    >
      <div class="rounded-2xl">
        <img
          :src="`/persona/${id}.webp`"
          class="aspect-1/1 h-14 rounded-2xl object-cover object-top"
        />
      </div>
      <div class="flex grow flex-col items-stretch gap-1">
        <div class="flex flex-row justify-between gap-2">
          <BaseHeading variant="h2">{{ fullName }}</BaseHeading>
        </div>
        <Badge class="bg-tertiary-container text-on-tertiary-container self-start">{{ id }}</Badge>
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
