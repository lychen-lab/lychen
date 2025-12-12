import { cva, type VariantProps } from 'class-variance-authority';

export interface Props {
  text: string;
}

export const paragraphVariants = cva('text-balance', {
  variants: {
    variant: {
      default: 'text-base leading-5',
      'website-default': 'text-base font-semibold',
      'website-highlight': 'text-lg font-semibold',
    },
  },
  defaultVariants: {
    variant: 'default',
  },
});

export type ParagraphVariants = VariantProps<typeof paragraphVariants>;
