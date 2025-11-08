<template>
  <div
    class="active:bg-surface-container-highest flex flex-row items-center justify-between gap-4 p-4"
  >
    <div class="flex flex-col gap-1 overflow-hidden">
      <span class="truncate text-sm font-bold">{{ landTask.title }} </span>
      <span
        v-if="landTask.startDate || landTask.dueDate"
        class="text-xs opacity-70"
      >
        {{
          landTask.startDate && !landTask.dueDate
            ? 'Débute le'
            : !landTask.startDate && landTask.dueDate
              ? 'Dû le'
              : 'Du'
        }}
        <span v-if="landTask.startDate">{{ d(landTask.startDate, 'short') }}</span>
        {{ landTask.startDate && landTask.dueDate ? 'au' : '' }}
        <span
          v-if="landTask.dueDate"
          :class="{ 'text-negative': overdue }"
          >{{ d(landTask.dueDate, 'short') }}</span
        >
      </span>
    </div>
    <div v-if="!noState">
      <BadgeTeraLandTaskState :state="landTask.state" />
    </div>
  </div>
</template>

<script lang="ts" setup>
import { computed } from 'vue';

import { messages, TRANSLATION_KEY } from '@lychen/i18n-tera/land';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import { VARIANT, type Variant } from '.';
import BadgeTeraLandTaskState from '../badges/state/BadgeTeraLandTaskState.vue';
import type { components } from '@lychen/typescript-tera-api-sdk/generated/tera-api';

const {
  variant = VARIANT.Default,
  landTask,
  noState = false,
} = defineProps<{
  landTask: components['schemas']['LandTask.jsonld'];
  noState?: boolean;
  variant?: Variant;
}>();

const { t, d } = useI18nExtended({ messages, rootKey: TRANSLATION_KEY, prefixed: true });

const overdue = computed<boolean>(() => {
  if (!landTask.dueDate) return false;
  return new Date(landTask.dueDate) < new Date();
});
</script>
