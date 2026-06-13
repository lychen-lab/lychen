import type { InjectionKey, Ref } from 'vue';

import type { RouteLocationAsPathGeneric, RouteLocationAsRelativeGeneric } from 'vue-router';

export const INJECTION_KEY_NAVIGATION_EXPANDED: InjectionKey<Ref<boolean>> = Symbol();

export interface NavigationItem {
  label: string;
  to: string | RouteLocationAsRelativeGeneric | RouteLocationAsPathGeneric;
  icon?: string;
}

export interface NavigationSection {
  label?: string;
}

export type Navigation = NavigationSection[];

export { default as LayoutInAppRoot } from './LayoutInAppRoot.vue';
export { default as LayoutInAppContent } from './LayoutInAppContent.vue';
export { default as LayoutInAppHeader } from './LayoutInAppHeader.vue';
export { default as LayoutInAppSideNavigation } from './LayoutInAppSideNavigation.vue';
export { default as LayoutInAppSideNavigationItem } from './LayoutInAppSideNavigationItem.vue';
export { default as LayoutInAppSideNavigationButtonExpand } from './LayoutInAppSideNavigationButtonExpand.vue';
export { default as LayoutInAppSideNavigationSection } from './LayoutInAppSideNavigationSection.vue';
export { default as LayoutInAppSideNavigationHeader } from './LayoutInAppSideNavigationHeader.vue';
export { default as LayoutInAppSideNavigationHeaderApp } from './LayoutInAppSideNavigationHeaderApp.vue';
export { default as LayoutInAppSideNavigationFooter } from './LayoutInAppSideNavigationFooter.vue';
