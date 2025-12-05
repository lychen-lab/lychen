<template>
  <LayoutMain>
    <template #navigation>
      <TheNavigation />
    </template>
    <template #header
      ><RouterLink
        :to="{ name: ROUTE_HOME.name }"
        class="flex flex-row items-stretch"
        ><LogoLychenFull class="h-10" /></RouterLink
    ></template>
    <template #mobile>
      <TheNavigationMobile />
    </template>
    <template #footer>
      <TheFooter />
    </template>
  </LayoutMain>
</template>

<script lang="ts" setup>
import { ROUTE_HOME } from '@/views/home';
import { defineAsyncComponent } from 'vue';

const LayoutMain = defineAsyncComponent(
  () => import('@lychen/vue-layouts/website-main/LayoutWebsiteMain.vue'),
);
const LogoLychenFull = defineAsyncComponent(
  () => import('@lychen/vue-components-extra/logo-lychen/LogoLychenFull.vue'),
);

const TheNavigation = defineAsyncComponent(() => import('./TheNavigation.vue'));
const TheNavigationMobile = defineAsyncComponent(() => import('./TheNavigationMobile.vue'));
const TheFooter = defineAsyncComponent(() => import('./TheFooter.vue'));
</script>
