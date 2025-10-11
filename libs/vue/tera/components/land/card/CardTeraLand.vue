<template>
  <Card
    hoverable
    class="h-full justify-between items-stretch active:bg-surface-container-highest"
  >
    <div class="flex flex-row gap-2 items-center justify-between opacity-70">
      <div>
        <IconUsers v-if="variant === VARIANT.Default && numberOfMember && numberOfMember > 1" />
      </div>
      <div
        v-if="altitude && altitude !== null"
        class="flex flex-row-reverse text-xs gap-2 items-center"
      >
        <IconMountain /> {{ t(`property.altitude.default`, altitude) }}
      </div>
    </div>
    <div class="flex flex-col gap-1">
      <div class="flex flex-row gap-4 items-center opacity-70">
        <small
          v-if="surface"
          class="text-tertiary"
          >{{ t('property.surface.default', surface) }}</small
        >
        <small
          v-if="numberOfArea && numberOfArea > 0"
          class="text-tertiary"
          >{{ t('property.land_areas.default', numberOfArea!) }}</small
        >
      </div>

      <BaseHeading variant="h3">{{ name }}</BaseHeading>
    </div>
  </Card>
</template>

<script lang="ts" setup>
import { defineAsyncComponent } from 'vue';

import { messages, TRANSLATION_KEY } from '@lychen/i18n-tera/land';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import { VARIANT, type Variant } from '.';
import { IconUsers, IconMountain } from '@lychen/vue-icons';

import Card from '@lychen/vue-components-core/card/Card.vue';

const BaseHeading = defineAsyncComponent(
  () => import('@lychen/vue-components-app/base-heading/BaseHeading.vue'),
);

withDefaults(
  defineProps<{
    name?: string;
    numberOfArea?: number;
    surface?: number | null;
    altitude?: number | null;
    numberOfMember?: number;
    variant?: Variant;
  }>(),
  {
    name: undefined,
    numberOfArea: 0,
    surface: undefined,
    altitude: undefined,
    numberOfMember: undefined,
    variant: VARIANT.Default,
  },
);

const { t } = useI18nExtended({ messages, rootKey: TRANSLATION_KEY, prefixed: true });
</script>
