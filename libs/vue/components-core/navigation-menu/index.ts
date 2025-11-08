import { cva } from 'class-variance-authority';

export { default as NavigationMenu } from './NavigationMenu.vue';
export { default as NavigationMenuContent } from './NavigationMenuContent.vue';
export { default as NavigationMenuIndicator } from './NavigationMenuIndicator.vue';
export { default as NavigationMenuItem } from './NavigationMenuItem.vue';
export { default as NavigationMenuLink } from './NavigationMenuLink.vue';
export { default as NavigationMenuList } from './NavigationMenuList.vue';
export { default as NavigationMenuTrigger } from './NavigationMenuTrigger.vue';
export { default as NavigationMenuViewport } from './NavigationMenuViewport.vue';

export const navigationMenuTriggerStyle = cva(
  'data-[active]:bg-tertiary/50 data-[state=open]:bg-tertiary/10 group inline-flex h-10 w-max cursor-pointer items-center justify-center rounded-full px-4 py-2 text-sm font-bold transition-colors focus:outline-none disabled:pointer-events-none disabled:opacity-50',
);

export const EVENT_NavigateToRoute = 'navigateToRoute';
