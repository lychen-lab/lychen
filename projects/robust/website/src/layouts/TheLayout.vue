<template>
  <LayoutWebsiteApplication
    :application-name="applicationName"
    :application-state="applicationState"
    :route-home="RoutePageHome"
  >
    <template #navigation>
      <TheNavigation />
    </template>
    <template #header><LogoLychenFull class="h-10" /></template>
    <template #mobile>
      <TheNavigationMobile />
    </template>
    <template #footerUnderLogo>
      <TheFooter />
    </template>
  </LayoutWebsiteApplication>
</template>

<script lang="ts" setup>
import { RoutePageHome } from '@/pages/home';
import { APPLICATION_ALIAS } from '@lychen/typescript-applications/constants/ApplicationAlias';
import { APPLICATIONS } from '@lychen/vue-applications/constants/Applications';
import { defineAsyncComponent } from 'vue';

const LayoutWebsiteApplication = defineAsyncComponent(
  () => import('@lychen/vue-layouts/website-app/LayoutWebsiteApplication.vue'),
);
const LogoLychenFull = defineAsyncComponent(
  () => import('@lychen/vue-components-extra/logo-lychen/LogoLychenFull.vue'),
);

const TheNavigation = defineAsyncComponent(() => import('./TheNavigation.vue'));
const TheNavigationMobile = defineAsyncComponent(() => import('./TheNavigationMobile.vue'));
const TheFooter = defineAsyncComponent(() => import('./TheFooter.vue'));

const applicationName = 'Robust';
const applicationState = APPLICATIONS[APPLICATION_ALIAS.Robust].state;
</script>
