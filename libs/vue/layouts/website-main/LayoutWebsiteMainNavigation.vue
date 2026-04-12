<template>
  <div class="z-50 flex h-[70px] w-full">
    <div
      class="backdrop text-on-surface-container relative container flex flex-row items-stretch gap-4 rounded-3xl px-4 py-2"
    >
      <slot></slot>

      <div class="flex flex-row items-center lg:hidden" v-if="mobileMenu">
        <Sheet
          v-model:open="isOpen"
          side="right"
        >
          <SheetTrigger as-child>
            <IconMenu class="cursor-pointer" />
          </SheetTrigger>
          <SheetContent
            class="bg-surface-container/70 text-on-surface-container w-full overflow-y-auto backdrop-blur-lg"
          >
            <template #header><slot name="header"></slot></template>
            <SheetClose class="flex flex-col gap-2 text-left">
              <slot name="mobile"></slot>
            </SheetClose>
          </SheetContent>
        </Sheet>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { defineAsyncComponent, provide, ref } from 'vue';
import IconMenu from '@lychen/vue-icons/IconMenu.vue';
import { SheetClose } from '@lychen/vue-components-core/sheet';

const SheetTrigger = defineAsyncComponent(
  () => import('@lychen/vue-components-core/sheet/SheetTrigger.vue'),
);

const Sheet = defineAsyncComponent(() => import('@lychen/vue-components-core/sheet/Sheet.vue'));

const SheetContent = defineAsyncComponent(
  () => import('@lychen/vue-components-core/sheet/SheetContent.vue'),
);

const isOpen = ref<boolean>(false);

provide('mobileMenuIsOpen', isOpen);

defineProps<{mobileMenu?: boolean;}>()
</script>

<style scoped>
.backdrop::before {
  content: '';
  position: absolute;
  width: 100%;
  height: 100%;
  top: 0;
  bottom: 0;
  left: 0;
  backdrop-filter: blur(var(--blur-lg));
  z-index: -1;
  background-color: oklch(from var(--color-surface-container) l c h / calc(alpha - 0.3));
  border-radius: var(--radius-3xl);
}
</style>
