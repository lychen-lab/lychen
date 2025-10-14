<template>
  <template v-if="navigationExpanded">
    <RouterLink
      :to="to"
      exact-active-class="bg-surface-container"
      :class="
        cn(
          'menu-item flex flex-row items-center justify-start gap-2 px-3 -mx-3 py-2 rounded-xl hover:bg-surface-container-highest',
          props.class,
        )
      "
    >
      <Icon
        v-if="icon"
        :icon="icon"
        class="h-5 w-5 shrink-0"
      />
      <span>{{ label }}</span>
      <slot />
    </RouterLink>
  </template>
  <template v-else>
    <Tooltip>
      <TooltipTrigger as-child>
        <RouterLink
          :to="to"
          exact-active-class="bg-surface-container"
          :class="
            cn(
              'menu-item flex flex-row items-center justify-start gap-4 px-3 -mx-3 py-2 rounded-xl hover:bg-surface-container-highest',
              props.class,
            )
          "
        >
          <span class="sr-only">{{ label }}</span>
        </RouterLink>
      </TooltipTrigger>
      <TooltipContent
        class="bg-surface-container text-on-surface-container z-30 rounded-lg px-2 py-1"
        side="right"
        :side-offset="20"
      >
        {{ label }}
      </TooltipContent>
    </Tooltip>
  </template>
</template>

<script lang="ts" setup>
import { inject, type HTMLAttributes } from 'vue';
import { INJECTION_KEY_NAVIGATION_EXPANDED, type NavigationItem } from '.';
import { Tooltip, TooltipTrigger, TooltipContent } from '@lychen/vue-components-core/tooltip';
import { cn } from '@lychen/typescript-utils/tailwind/Cn';

const props = defineProps<NavigationItem & { class?: HTMLAttributes['class'] }>();

const navigationExpanded = inject(INJECTION_KEY_NAVIGATION_EXPANDED);
</script>
