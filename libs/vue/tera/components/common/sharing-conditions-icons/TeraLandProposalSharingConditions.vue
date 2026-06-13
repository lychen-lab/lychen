<template>
  <div
    v-if="sharingConditions"
    class="flex"
    :class="display === DISPLAY.Horizontal ? 'flex-row' : 'flex-col'"
  >
    <div
      v-for="(condition, index) in [...sharingConditions].sort((a, b) => a.localeCompare(b))"
      :key="condition"
      class="bg-surface-container-highest flex items-center gap-2 self-start rounded-2xl p-2"
      :class="`z-${index} ${iconsBorderClass} ${display === DISPLAY.Horizontal ? '-ml-2' : ''}`"
      :style="{
        background: `oklch(from var(--color-surface-container-highest) l calc(c * ${index}) h)`,
      }"
    >
      <Icon :icon="LAND_SHARING_CONDITION_ICON[condition]" />
      <span v-if="displayLabel && display === DISPLAY.Vertical">{{
        t(`property.sharing_conditions.options.${condition}`)
      }}</span>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { messages, TRANSLATION_KEY } from '@lychen/i18n-tera/land-proposal';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import { type Display, DISPLAY } from '.';
import { type LandSharingCondition } from '@lychen/typescript-tera-core/constants/LandSharingCondition';
import { LAND_SHARING_CONDITION_ICON } from '@lychen/vue-tera/components/icons/IconLandSharingCondition';

const {
  display = DISPLAY.Horizontal,
  sharingConditions = [],
  iconsBorderClass = 'border-3 border-surface-container-low',
  displayLabel = false,
} = defineProps<{
  display?: Display;
  sharingConditions?: LandSharingCondition[] | null;
  iconsBorderClass?: string;
  displayLabel?: boolean;
}>();

const { t } = useI18nExtended({ messages, rootKey: TRANSLATION_KEY, prefixed: true });
</script>
