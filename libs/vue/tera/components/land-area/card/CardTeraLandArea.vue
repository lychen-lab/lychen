<template>
  <div
    class="bg-surface-container-high text-on-surface-container active:bg-surface-container-highest flex flex-row items-center justify-between gap-4 rounded-3xl p-4"
  >
    <div class="flex flex-col gap-1 overflow-hidden">
      <BaseHeading
        class="truncate"
        variant="h3"
        >{{ landArea.name }}
      </BaseHeading>
      <BadgeTeraLandAreaKind
        :kind="landArea.kind"
        class="self-start"
      />
    </div>
  </div>
</template>

<script lang="ts" setup>
import type { components } from '@lychen/typescript-tera-api-sdk/generated/tera-api';
import { VARIANT, type Variant } from '.';
import BadgeTeraLandAreaKind from '../badges/kind/BadgeTeraLandAreaKind.vue';
import BaseHeading from '@lychen/vue-components-app/base-heading/BaseHeading.vue';

const { variant = VARIANT.Default, landArea } = defineProps<{
  landArea: components['schemas']['LandArea.jsonld'];
  variant?: Variant;
}>();
</script>
