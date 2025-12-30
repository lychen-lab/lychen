<template>
  <LayoutMain>
    <template #navigation>
      <div class="flex w-full flex-row items-center justify-between">
        <div>
          <RouterLink
            :to="{ name: ROUTE_HOME.name }"
            class="flex flex-row"
          >
            <div class="font-lexend flex flex-row items-center gap-0">
              <Icon />
              <span class="text-on-surface font-medium tracking-normal">espace</span>
              <span class="text-on-surface/40">.lychen</span>
            </div>
          </RouterLink>
        </div>
        <div class="flex flex-row items-center gap-2">
          <RouterLink :to="{ name: ROUTE_HOME.name }">
            <Button size="xs">Partager mon terrain</Button>
          </RouterLink>
          <RouterLink :to="{ name: ROUTE_HOME.name }">
            <Button
              size="xs"
              variant="ghost"
              >Connexion</Button
            >
          </RouterLink>
        </div>
      </div>
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
import TheFooter from './TheFooter.vue';
import { ROUTE_HOME } from '@/views/home';
import { Button } from '@lychen/vue-components-core/button';
import LayoutMain from '@lychen/vue-layouts/website-main/LayoutWebsiteMain.vue';
</script>
