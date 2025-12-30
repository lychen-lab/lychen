<template>
  <button
    :class="
      cn(
        'cursor-pointer inline-flex items-center justify-center whitespace-nowrap text-sm font-medium transition-colors gap-2',
        'disabled:opacity-50 disabled:cursor-not-allowed',
        PRESETS.FocusOutline,
        VARIANT_VALUES[variant],
        SIZE_VALUES[size],
        loading ? 'cursor-progress' : '',
        props.class,
        `lychen-button ${innerRound ? 'lychen-button--round' : ''}`,
      )
    "
    :aria-label="label"
    :disabled="loading"
  >
    <template v-if="ICON_POSITION.Start === iconPosition && !loading">
      <slot name="icon" />
    </template>
    <template v-if="!iconOnly">
      <slot>{{ label }}</slot>
    </template>
    <template v-if="ICON_POSITION.End === iconPosition && !loading">
      <slot name="icon" />
    </template>
    <IconLoaderCircle v-if="loading" />
  </button>
</template>

<script setup lang="ts">
import { computed, type HTMLAttributes, type VNode } from 'vue';
import { PRESETS } from '../utils/Preset';
import { cn } from '@lychen/typescript-utils/tailwind/Cn';
import {
  ICON_POSITION,
  SIZE_VALUES,
  VARIANT_VALUES,
  type IconPosition,
  type VariantKey,
  type SizeKey,
} from '.';
import IconLoaderCircle from '@lychen/vue-icons/IconLoaderCircle.vue';

interface Props {
  /**
   * Text to display
   */
  label?: string;
  /**
   * Predefined variants
   */
  variant?: VariantKey;
  /**
   * Predefined sizes
   */
  size?: SizeKey;
  /**
   * CSS Class
   */
  class?: HTMLAttributes['class'];
  /**
   * Position of the icon againts the label
   */
  iconPosition?: IconPosition;
  /**
   * Feedback for user clicking on the button, disable the button when active
   */
  loading?: boolean;

  iconOnly?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  size: 'default',
  variant: 'default',
  class: undefined,
  label: undefined,
  iconPosition: () => ICON_POSITION.End,
  loading: false,
  iconOnly: false,
});

const innerRound = computed(() => {
  if (props.iconOnly) {
    return true;
  }

  return false;
});

defineSlots<{
  /**
   * Default slot for the label
   */
  default: () => VNode[];
  /**
   * Before the label
   */
  icon: () => VNode[];
}>();
</script>

<style lang="css" scoped>
.lychen-button--round {
  aspect-ratio: 1/1;
  padding: calc(var(--spacing) * 2);
}
</style>
