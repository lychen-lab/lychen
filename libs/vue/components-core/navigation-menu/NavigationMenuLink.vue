<template>
  <NavigationMenuLink v-bind="forwarded" :class="cn('hover:bg-tertiary/10', props.class)">
    <slot />
  </NavigationMenuLink>
</template>

<script setup lang="ts">
import { cn } from '@lychen/typescript-utils/tailwind/Cn';
import {
  NavigationMenuLink,
  type NavigationMenuLinkEmits,
  type NavigationMenuLinkProps,
  useForwardPropsEmits,
} from 'reka-ui';
import { computed, type HTMLAttributes } from 'vue';

const emits = defineEmits<NavigationMenuLinkEmits>();

const props = defineProps<NavigationMenuLinkProps & { class?: HTMLAttributes['class'] }>();

const delegatedProps = computed(() => {
  const { class: _, ...delegated } = props;

  return delegated;
});

const forwarded = useForwardPropsEmits(delegatedProps, emits);
</script>
