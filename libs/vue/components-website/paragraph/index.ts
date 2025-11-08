import { cva, type VariantProps } from 'class-variance-authority';

export interface Props {
  text: string;
}

export const paragraphVariants = cva('text-balance', {
  variants: {
    variant: {
      default: 'text-base leading-4',
      'website-default': 'text-base leading-6 font-semibold tracking-tight',
      'website-highlight': 'text-lg leading-6 font-semibold tracking-tight',
    },
  },
  defaultVariants: {
    variant: 'default',
  },
});

export type ParagraphVariants = VariantProps<typeof paragraphVariants>;
