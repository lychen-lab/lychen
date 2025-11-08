<template>
  <Popover>
    <Tooltip>
      <TooltipTrigger>
        <PopoverTrigger as-child>
          <Button
            icon-only
            size="sm"
            variant="ghost"
            :icon-position="ICON_POSITION.Start"
          >
            <template #icon>
              <IconGrip />
            </template>
          </Button>
        </PopoverTrigger>
      </TooltipTrigger>
      <PopoverContent class="flex flex-col gap-4">
        <h2 class="self-center p-2 font-medium">Lychen écosystème</h2>
        <div class="grid grid-cols-3 place-items-center gap-4">
          <AppMenuIcon
            v-for="app in filteredApps"
            :key="app.id"
            v-bind="app"
          />
        </div>
        <hr class="w-2/3 self-center opacity-30" />
        <p class="self-center text-sm opacity-70">
          Découvrir toutes les applications <IconArrowUpRight />
        </p>
      </PopoverContent>
      <TooltipContent> Applications </TooltipContent>
    </Tooltip>
  </Popover>
</template>

<script lang="ts" setup>
import { ICON_POSITION } from '@lychen/vue-components-core/button';
import Button from '@lychen/vue-components-core/button/Button.vue';
import { Popover, PopoverContent, PopoverTrigger } from '@lychen/vue-components-core/popover';
import { Tooltip, TooltipContent, TooltipTrigger } from '@lychen/vue-components-core/tooltip';
import IconGrip from '@lychen/vue-icons/IconGrip.vue';
import AppMenuIcon from './AppMenuIcon.vue';
import IconArrowUpRight from '@lychen/vue-icons/IconArrowUpRight.vue';
import { computed } from 'vue';
import { ECOSYSTEM_APPS } from './apps';

const apps = ECOSYSTEM_APPS;

const currentAppId = import.meta.env.VITE_APP_ID;
const filteredApps = computed(() => {
  if (!currentAppId) {
    return apps;
  }
  return apps.filter((app) => app.id !== currentAppId);
});
</script>
