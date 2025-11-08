import { cva, type VariantProps } from 'class-variance-authority';

export { default as Toggle } from './Toggle.vue';

export const toggleVariants = cva(
  'ring-offset-background focus-visible:bg-surface-container focus-visible:border-on-surface/70 focus-visible:text-on-surface-container data-[state=on]:bg-on-surface data-[state=on]:text-surface inline-flex cursor-pointer items-center justify-center gap-2 rounded-xl text-sm font-medium transition-colors focus-visible:outline-none disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0',
  {
    variants: {
      variant: {
        default: 'bg-surface-container text-on-surface-container',
        outline:
          'border-on-surface/40 bg-surface hover:bg-surface-container hover:border-on-surface/70 hover:text-on-surface-container border',
      },
      size: {
        default: 'h-10 min-w-10 px-3',
        sm: 'h-9 min-w-9 px-2.5',
        lg: 'h-11 min-w-11 px-5',
      },
    },
    defaultVariants: {
      variant: 'default',
      size: 'default',
    },
  },
);

export type ToggleVariants = VariantProps<typeof toggleVariants>;
