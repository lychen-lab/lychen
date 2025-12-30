import { cva, type VariantProps } from 'class-variance-authority';

export interface Props {
  text: string;
}

export const titleVariants = cva(' font-inter text-balance antialiased', {
  variants: {
    variant: {
      default: 'text-base',
      h1: 'text-5xl font-extrabold lg:text-7xl',
      h2: 'text-3xl font-bold lg:text-5xl',
      h3: 'text-xl font-bold lg:text-2xl',
      h4: 'text-md font-bold lg:text-xl',
      h5: 'font-bold lg:text-2xl',
      h6: 'font-bold lg:text-xl',
    },
  },
  defaultVariants: {
    variant: 'default',
  },
});

export type TitleVariants = VariantProps<typeof titleVariants>;

export { default as Title } from './Title.vue';
