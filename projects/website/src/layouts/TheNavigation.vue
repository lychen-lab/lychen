<template>
  <div class="flex w-full flex-row items-stretch justify-between gap-4">
    <RouterLink
      :to="{ name: ROUTE_HOME.name }"
      class="flex flex-row items-stretch"
    >
      <LogoLychenFull class="hover:text-on-primary-container"
    /></RouterLink>
    <div class="flex flex-row items-stretch gap-2">
      <NavigationMenu class="hidden lg:flex">
        <NavigationMenuList>
          <NavigationMenuItem>
            <NavigationMenuTrigger>{{ t(`navigation.app.title`) }}</NavigationMenuTrigger>
            <NavigationMenuContent>
              <div
                class="bg-surface-container/70 text-on-surface-container flex flex-col items-stretch gap-2 backdrop-blur-lg"
              >
                <div class="flex flex-row gap-4">
                  <div
                    class="grid basis-2/3 gap-4 p-6 md:w-[600px] md:grid-cols-2 lg:w-[800px] lg:grid-cols-2"
                  >
                    <NavigationMenuSubLink
                      v-for="application in opiniatedApplicationsList"
                      :key="application.title"
                      :title="application.title"
                      :description="application.description"
                    />
                  </div>

                  <div
                    class="bg-tertiary-container relative flex basis-1/3 flex-col gap-2 rounded-3xl"
                  >
                    <img
                      class="absolute top-0 left-0 h-full w-full rounded-3xl opacity-50"
                      src="/robust/covers/robust-cover-1.webp"
                    />
                    <div
                      class="from-tertiary-container to-tertiary-container/20 absolute top-0 left-0 z-20 h-full w-full rounded-3xl bg-gradient-to-br"
                    ></div>
                    <div class="text-on-surface z-20 flex flex-col gap-4 p-8">
                      <p class="text-md font-lexend leading-none font-black tracking-wide">
                        {{ robust.title }}
                      </p>
                      <p>{{ robust.description }}</p>

                      <a href="https://robust.lychen.org">
                        <Button
                          :label="t('navigation.app.robust.button')"
                          class="self-start"
                        >
                          <template #icon> <IconArrowUpRight /> </template></Button
                      ></a>
                    </div>
                  </div>
                </div>
              </div>
            </NavigationMenuContent>
          </NavigationMenuItem>

          <NavigationMenuItem>
            <NavigationMenuTrigger>{{ t(`navigation.resources.title`) }}</NavigationMenuTrigger>
            <NavigationMenuContent>
              <div
                class="bg-surface-container/70 text-on-surface-container flex flex-row gap-2 backdrop-blur-lg md:w-[500px]"
              >
                <div class="flex basis-1/2 flex-col items-stretch gap-4 p-6">
                  <NavigationMenuSubLink
                    v-for="resourceMenu in resourcesMenuList"
                    v-bind="resourceMenu"
                    :key="resourceMenu.title"
                  />
                </div>
                <div class="basis-1/2">
                  <img
                    :src="ResourcesMenuUrl"
                    class="h-full max-h-[500px] w-auto"
                  />
                </div>
              </div>
            </NavigationMenuContent>
          </NavigationMenuItem>
          <NavigationMenuItem>
            <NavigationMenuTrigger>{{ t(`navigation.community.title`) }}</NavigationMenuTrigger>
            <NavigationMenuContent>
              <div
                class="bg-surface-container/70 text-on-surface-container flex flex-row gap-4 backdrop-blur-lg md:w-[500px]"
              >
                <div class="flex basis-1/2 flex-col items-stretch gap-4 p-6">
                  <NavigationMenuSubLink
                    v-for="communityMenu in communityMenuList"
                    v-bind="communityMenu"
                    :key="communityMenu.title"
                  />
                </div>
                <div class="basis-1/2">
                  <img
                    :src="CommunityMenuUrl"
                    class="h-full max-h-[500px] w-auto"
                  />
                </div>
              </div>
            </NavigationMenuContent>
          </NavigationMenuItem>

          <NavigationMenuItem>
            <RouterLink :to="{ name: ROUTE_SPONSOR.name }">
              <NavigationMenuLink
                as="div"
                :class="navigationMenuTriggerStyle()"
                class="hover:bg-primary-container/30 hover:text-on-primary-container"
              >
                {{ t(`navigation.sponsor.title`) }}
              </NavigationMenuLink>
            </RouterLink>
          </NavigationMenuItem>
        </NavigationMenuList>
      </NavigationMenu>
    </div>

    <div class="flex flex-row items-center gap-2">
      <SelectLanguage />
      <ToggleColorScheme />
      <a
        :href="SOCIAL_LINK.GitHub"
        target="_blank"
        aria-label="GitHub"
      >
        <IconGithub />
      </a>
      <a
        :href="SOCIAL_LINK.Discord"
        target="_blank"
        aria-label="Discord"
      >
        <IconDiscord />
      </a>
      <ButtonTallyPreregister class="hidden md:flex" />
    </div>
  </div>
</template>

<script setup lang="ts">
import IconGithub from '@lychen/vue-icons/IconGithub.vue';
import IconDiscord from '@lychen/vue-icons/IconDiscord.vue';
import CommunityMenuUrl from './assets/community-menu.webp';
import ResourcesMenuUrl from './assets/resources-menu.webp';
import { navigationMenuTriggerStyle } from '@lychen/vue-components-core/navigation-menu';
import { computed, defineAsyncComponent } from 'vue';
import { ROUTE_HOME } from '@/views/home';
import { useApplicationsCatalog } from '@lychen/vue-applications/composables/useApplicationsCatalog';
import { SOCIAL_LINK } from '@lychen/typescript-constants/Social';
import { messages, TRANSLATION_KEY } from './i18n';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import IconArrowUpRight from '@lychen/vue-icons/IconArrowUpRight.vue';

import { useCommunityMenu } from './composables/useCommunityMenu';
import { useResourcesMenu } from './composables/useResourcesMenu';
import { ROUTE_SPONSOR } from '@/views/sponsor';

import ButtonTallyPreregister from '@lychen/vue-components-website/button-tally-preregister/ButtonTallyPreregister.vue';
import LogoLychenFull from '@lychen/vue-components-extra/logo-lychen/LogoLychenFull.vue';
import { APPLICATION_ALIAS } from '@lychen/typescript-applications/constants/ApplicationAlias';
import Button from '@lychen/vue-components-core/button/Button.vue';

import ToggleColorScheme from '@lychen/vue-color-scheme/components/ToggleColorScheme.vue';

const SelectLanguage = defineAsyncComponent(
  () => import('@lychen/vue-i18n/components/select-language/SelectLanguage.vue'),
);

const NavigationMenuSubLink = defineAsyncComponent(
  () => import('@lychen/vue-components-core/navigation-menu/NavigationMenuSubLink.vue'),
);

const NavigationMenu = defineAsyncComponent(
  () => import('@lychen/vue-components-core/navigation-menu/NavigationMenu.vue'),
);
const NavigationMenuContent = defineAsyncComponent(
  () => import('@lychen/vue-components-core/navigation-menu/NavigationMenuContent.vue'),
);
const NavigationMenuItem = defineAsyncComponent(
  () => import('@lychen/vue-components-core/navigation-menu/NavigationMenuItem.vue'),
);

const NavigationMenuLink = defineAsyncComponent(
  () => import('@lychen/vue-components-core/navigation-menu/NavigationMenuLink.vue'),
);

const NavigationMenuList = defineAsyncComponent(
  () => import('@lychen/vue-components-core/navigation-menu/NavigationMenuList.vue'),
);

const NavigationMenuTrigger = defineAsyncComponent(
  () => import('@lychen/vue-components-core/navigation-menu/NavigationMenuTrigger.vue'),
);

const { t } = useI18nExtended({ messages, rootKey: TRANSLATION_KEY, prefixed: true });

const { opiniatedApplicationsList, getAppInfo } = useApplicationsCatalog();

const { communityMenuList } = useCommunityMenu();
const { resourcesMenuList } = useResourcesMenu();

const robust = computed(() => getAppInfo(APPLICATION_ALIAS.Robust));
</script>
