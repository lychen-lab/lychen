<template>
  <DialogPortal>
    <DialogOverlay
      class="data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:fade-out-0 data-[state=open]:fade-in-0 fixed inset-0 z-50"
    />
    <DialogContent
      :class="cn(sheetVariants({ side }), props.class)"
      v-bind="{ ...forwarded, ...$attrs }"
    >
      <div class="flex flex-row items-center justify-between">
        <slot name="header"></slot>
        <DialogClose
          class="focus:ring-ring data-[state=open]:bg-secondary rounded-sm opacity-70 transition-opacity hover:opacity-100 focus:ring-offset-2 focus:outline-none disabled:pointer-events-none"
        >
          <IconTimes class="size-4" />
        </DialogClose>
      </div>

      <slot />
    </DialogContent>
  </DialogPortal>
</template>

<script setup lang="ts">
import {
  DialogContent,
  type DialogContentEmits,
  type DialogContentProps,
  DialogOverlay,
  DialogPortal,
  DialogClose,
  useForwardPropsEmits,
} from 'reka-ui';
import { computed, type HTMLAttributes } from 'vue';

import { cn } from '@lychen/typescript-utils/tailwind/Cn';
import { type SheetVariants, sheetVariants } from '.';
import IconTimes from '@lychen/vue-icons/IconTimes.vue';

interface SheetContentProps extends DialogContentProps {
  class?: HTMLAttributes['class'];
  side?: SheetVariants['side'];
}

defineOptions({
  inheritAttrs: false,
});

const props = defineProps<SheetContentProps>();

const emits = defineEmits<DialogContentEmits>();

const delegatedProps = computed(() => {
  const { class: _, side, ...delegated } = props;

  return delegated;
});

const forwarded = useForwardPropsEmits(delegatedProps, emits);
</script>
