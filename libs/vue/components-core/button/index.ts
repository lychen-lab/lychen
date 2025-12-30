import { type ObjectValues } from '@lychen/typescript-utils/transformers/ObjectValues';

export const ICON_POSITION = {
  Start: 'start',
  End: 'End',
} as const;

export type IconPosition = ObjectValues<typeof ICON_POSITION>;

export const VARIANT_VALUES = {
  default: 'bg-on-surface text-surface hover:bg-on-surface/90',
  negative: 'bg-negative text-on-negative hover:bg-negative/90',
  positive: 'bg-positive text-on-positive hover:bg-positive/90',
  warning: 'bg-warning text-on-warning hover:bg-warning/90',
  ghost: 'hover:bg-surface-container hover:text-on-surface-container',
  outline:
    'border-2 border-on-surface/50 bg-surface-container-lowest hover:bg-surface-container-low',
} as const;

export const VARIANT = Object.keys(VARIANT_VALUES);
export type VariantKey = keyof typeof VARIANT_VALUES;

export const SIZE_VALUES = {
  default: 'h-10 px-4 py-2 rounded-2xl text-md',
  xs: 'h-7 px-2 rounded-xl text-xs',
  sm: 'h-9 px-3 rounded-2xl text-sm',
  lg: 'h-11 px-8 rounded-2xl',
} as const;

export const SIZE = Object.keys(SIZE_VALUES);
export type SizeKey = keyof typeof SIZE_VALUES;

export { default as Button } from './Button.vue';
