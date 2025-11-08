import { cva, type VariantProps } from 'class-variance-authority';

export { default as BaseHeading } from './BaseHeading.vue';

export const baseHeadingVariants = cva('font-poppins text-on-surface text-balance antialiased', {
  variants: {
    variant: {
      h1: 'text-2xl font-bold',
      h2: 'text-xl font-bold',
      h3: 'text-lg font-bold',
      h4: 'text-md font-bold',
      h5: 'text-md font-medium',
      h6: 'text-md font-normal',
    },
  },
  defaultVariants: {
    variant: 'h1',
  },
});

export type BaseHeadingVariants = VariantProps<typeof baseHeadingVariants>;
