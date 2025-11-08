<template>
  <div class="z-50 flex w-full">
    <div class="text-on-surface-container flex w-full flex-row gap-4 rounded-full md:container">
      <a
        ref="lychenLogo"
        href="https://lychen.org"
        target="_blank"
        class="bg-surface-container/70 group hover:text-on-primary-container flex size-[56px] flex-row items-center justify-center rounded-3xl p-4 backdrop-blur-lg transition-all duration-500 ease-in-out hover:w-36"
        aria-label="lychen.org"
      >
        <div class="flex flex-col items-end">
          <div class="flex flex-row items-center">
            <LogoLychenIconOnly class="h-[1lh]" />
            <LogoLychenTextOnly class="hidden group-hover:flex" />
          </div>
          <small
            class="motion-blur-in-md motion-duration-[1s] motion-ease-spring-smooth hidden flex-row items-center gap-1 text-xs group-hover:flex"
            >lychen.org<IconArrowUpRight
          /></small>
        </div>
      </a>

      <div
        class="backdrop text-on-surface-container relative flex grow flex-row items-stretch justify-between gap-4 rounded-full px-6 py-2 shadow-lg"
      >
        <div class="flex flex-row items-center justify-start gap-2">
          <RouterLink
            :to="routeHome"
            class="flex flex-row items-center justify-start gap-1"
          >
            <ApplicationTitle :value="applicationName" />
          </RouterLink>
          <ApplicationBadgeState
            :state="applicationState"
            class="hidden md:flex"
          ></ApplicationBadgeState>
        </div>

        <div class="flex flex-row items-stretch gap-2">
          <slot></slot>
        </div>

        <div class="flex flex-row items-center gap-4">
          <SelectLanguage />
          <ToggleColorScheme />
          <a
            :href="SOCIAL_LINK.GitHub"
            target="_blank"
            aria-label="GitHub"
          >
            <IconGithub />
          </a>
          <ButtonTallyPreregister class="hidden md:flex" />
          <div class="flex flex-row items-center lg:hidden">
            <Sheet
              v-model:open="isOpen"
              side="right"
            >
              <SheetTrigger as-child>
                <IconMenu class="cursor-pointer" />
              </SheetTrigger>
              <SheetContent
                class="bg-surface-container/70 text-on-surface-container flex w-full flex-col gap-4 backdrop-blur-lg"
              >
                <template #header>
                  <div class="flex flex-col gap-1">
                    <ApplicationTitle :value="applicationName" />
                    <p>by Lychen</p>
                  </div></template
                >
                <slot name="mobile"></slot>
                <ButtonTallyPreregister />
              </SheetContent>
            </Sheet>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { defineAsyncComponent, provide, ref } from 'vue';
import IconArrowUpRight from '@lychen/vue-icons/IconArrowUpRight.vue';
import IconGithub from '@lychen/vue-icons/IconGithub.vue';
import { SOCIAL_LINK } from '@lychen/typescript-constants/Social';

import { type LayoutWebsiteApplicationNavigationProps } from '.';

import ToggleColorScheme from '@lychen/vue-color-scheme/components/ToggleColorScheme.vue';
import IconMenu from '@lychen/vue-icons/IconMenu.vue';

const ButtonTallyPreregister = defineAsyncComponent(
  () =>
    import('@lychen/vue-components-website/button-tally-preregister/ButtonTallyPreregister.vue'),
);
const LogoLychenIconOnly = defineAsyncComponent(
  () => import('@lychen/vue-components-extra/logo-lychen/LogoLychenIconOnly.vue'),
);

const LogoLychenTextOnly = defineAsyncComponent(
  () => import('@lychen/vue-components-extra/logo-lychen/LogoLychenTextOnly.vue'),
);

const ApplicationTitle = defineAsyncComponent(
  () => import('@lychen/vue-applications/components/ApplicationTitle.vue'),
);
const ApplicationBadgeState = defineAsyncComponent(
  () => import('@lychen/vue-applications/components/ApplicationBadgeState.vue'),
);

const SheetTrigger = defineAsyncComponent(
  () => import('@lychen/vue-components-core/sheet/SheetTrigger.vue'),
);

const Sheet = defineAsyncComponent(() => import('@lychen/vue-components-core/sheet/Sheet.vue'));

const SheetContent = defineAsyncComponent(
  () => import('@lychen/vue-components-core/sheet/SheetContent.vue'),
);

const SelectLanguage = defineAsyncComponent(
  () => import('@lychen/vue-i18n/components/select-language/SelectLanguage.vue'),
);

const isOpen = ref<boolean>(false);

provide('mobileMenuIsOpen', isOpen);

const lychenLogo = ref();

defineProps<LayoutWebsiteApplicationNavigationProps>();
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
