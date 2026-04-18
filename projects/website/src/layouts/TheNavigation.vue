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
            <RouterLink :to="i18nRoute({ name: ROUTE_MISSION.name })">
              <NavigationMenuLink
                as="div"
                :class="navigationMenuTriggerStyle()"
              >
                {{ t(`navigation.mission.title`) }}
              </NavigationMenuLink>
            </RouterLink>
          </NavigationMenuItem>
          <NavigationMenuItem>
            <NavigationMenuTrigger>{{ t(`navigation.initiatives.title`) }}</NavigationMenuTrigger>
            <NavigationMenuContent
              class="bg-surface-container/70 grid grid-cols-2 gap-4 rounded-lg p-4 backdrop-blur-lg md:w-[600px]"
            >
              <div class="h-50 overflow-hidden rounded-lg">
                <img
                  src="https://res.cloudinary.com/ddhvfiezg/image/upload/v1765700848/019b1bf9-0a48-73c9-ae92-1956fc25fe64.webp"
                  class="h-full w-full object-cover"
                  alt=""
                  loading="lazy"
                  decoding="async"
                />
              </div>
              <div class="flex flex-col gap-4">
                <NavigationMenuSubLink
                  :title="t('navigation.applications.title')"
                  :description="t('navigation.applications.description')"
                  :route="{ name: ROUTE_APPLICATIONS.name }"
                />

                <NavigationMenuSubLink
                  :title="t('navigation.label.title')"
                  :description="t('navigation.label.description')"
                  :route="{ name: ROUTE_LABEL.name }"
                />
              </div>
            </NavigationMenuContent>
          </NavigationMenuItem>
          <NavigationMenuItem>
            <NavigationMenuTrigger>{{ t(`navigation.about.title`) }}</NavigationMenuTrigger>
            <NavigationMenuContent
              class="bg-surface-container/70 grid grid-cols-2 gap-4 rounded-lg p-4 backdrop-blur-lg md:w-[600px]"
            >
              <div class="flex flex-col justify-between gap-4">
                <NavigationMenuSubLink
                  :title="t('navigation.team.title')"
                  :description="t('navigation.team.description')"
                  :route="{ name: ROUTE_TEAM.name }"
                />
                <NavigationMenuSubLink
                  :title="t('navigation.charter.title')"
                  :description="t('navigation.charter.description')"
                  :route="{ name: ROUTE_CHARTER.name }"
                />
                <NavigationMenuSubLink
                  :title="t('navigation.partnerships.title')"
                  :description="t('navigation.partnerships.description')"
                  :route="{ name: ROUTE_PARTNERSHIPS.name }"
                />
              </div>
              <div class="h-70 overflow-hidden rounded-lg">
                <img
                  src="https://res.cloudinary.com/ddhvfiezg/image/upload/v1765700238/019b1bef-b594-7bc0-84b8-2294798cc1f5.webp"
                  class="h-full w-full object-cover"
                  alt=""
                  loading="lazy"
                  decoding="async"
                />
              </div>
            </NavigationMenuContent>
          </NavigationMenuItem>
          <NavigationMenuItem>
            <a
              :href="LINK.Blog"
              target="_blank"
              rel="noopener noreferrer"
            >
              <NavigationMenuLink
                as="div"
                :class="navigationMenuTriggerStyle()"
              >
                {{ t(`navigation.blog.title`) }}
              </NavigationMenuLink>
            </a>
          </NavigationMenuItem>
        </NavigationMenuList>
      </NavigationMenu>
    </div>

    <div class="flex flex-row items-center gap-2">
      <a
        href="https://tally.so/r/D4vJ85"
        target="_blank"
        rel="noopener noreferrer"
      >
        <Button
          :label="t('preregistration')"
          size="sm"
          variant="outlined"
        />
      </a>
      <a
        :href="LINK.HelloAssoDonation"
        target="_blank"
      >
        <Button
          :label="t('donate')"
          size="sm"
        >
          <template #icon>
            <IconHeartHandshake />
          </template>
        </Button>
      </a>
    </div>
  </div>
</template>

<script setup lang="ts">
import { navigationMenuTriggerStyle } from '@lychen/vue-components-core/navigation-menu';
import { defineAsyncComponent } from 'vue';
import { ROUTE_HOME } from '@/views/home';
import { CONFIG } from './i18n';
import { usePrefixedI18n } from '@lychen/vue-i18n/composables/useI18nExtended';

import LogoLychenFull from '@lychen/vue-components-extra/logo-lychen/LogoLychenFull.vue';
import Button from '@lychen/vue-components-core/button/Button.vue';
import { LINK } from '@lychen/typescript-constants/Link';
import { ROUTE_PARTNERSHIPS } from '@/views/partnerships';
import { ROUTE_CHARTER } from '@/views/charter';
import { ROUTE_TEAM } from '@/views/team';
import { ROUTE_MISSION } from '@/views/mission';
import { ROUTE_APPLICATIONS } from '@/views/applications';
import IconHeartHandshake from '@lychen/vue-icons/IconHeartHandshake.vue';
import { ROUTE_LABEL } from '@/views/label';

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

const { t, i18nRoute } = usePrefixedI18n(CONFIG);
</script>
