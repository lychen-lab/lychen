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
              <span class="text-on-surface font-medium tracking-normal">espace</span>
              <span class="text-on-surface/40">.lychen</span>
            </div>
          </RouterLink>
        </div>
        <div class="flex flex-row items-center gap-2">
          <a
            :href="LINK.LandProposalForm"
            target="_blank"
          >
            <Button size="xs">Partager mon espace</Button>
          </a>
          <a
            :href="LINK.LandRequestForm"
            target="_blank"
          >
            <Button
              size="xs"
              class="bg-surface-container-highest text-on-surface"
              >Trouver un espace</Button
            >
          </a>
        </div>
      </div>
    </template>
    <template #header></template>
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
import { LINK } from '@lychen/typescript-espace/constants/App';
import { Button } from '@lychen/vue-components-core/button';
import LayoutMain from '@lychen/vue-layouts/website-main/LayoutWebsiteMain.vue';
</script>
