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
            <NavigationMenuTrigger>{{ t(`navigation.initiatives.title`) }}</NavigationMenuTrigger>
            <NavigationMenuContent
              class="bg-surface-container/70 grid grid-cols-2 gap-4 rounded-lg p-4 backdrop-blur-lg md:w-[600px]"
            >
              <NavigationMenuSubLink
                :title="t('navigation.applications.title')"
                :description="t('navigation.applications.description')"
                :route="{ name: ROUTE_APPLICATIONS.name }"
              />
              <NavigationMenuSubLink
                :title="t('navigation.api.title')"
                :description="t('navigation.api.description')"
              />
              <NavigationMenuSubLink
                :title="t('navigation.label.title')"
                :description="t('navigation.label.description')"
              />
              <NavigationMenuSubLink
                :title="t('navigation.code.title')"
                :description="t('navigation.code.description')"
                :link="SOCIAL_LINK.GitHub"
                rel="noopener noreferrer"
              />
            </NavigationMenuContent>
          </NavigationMenuItem>
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
            <NavigationMenuTrigger>{{ t(`navigation.about.title`) }}</NavigationMenuTrigger>
            <NavigationMenuContent
              class="bg-surface-container/70 grid grid-cols-2 gap-4 rounded-lg p-4 backdrop-blur-lg md:w-[600px]"
            >
              <NavigationMenuSubLink
                :title="t('navigation.charter.title')"
                :description="t('navigation.charter.description')"
                :route="{ name: ROUTE_CHARTER.name }"
              />

              <NavigationMenuSubLink
                :title="t('navigation.team.title')"
                :description="t('navigation.team.description')"
                :route="{ name: ROUTE_TEAM.name }"
              />

              <NavigationMenuSubLink
                :title="t('navigation.partnerships.title')"
                :description="t('navigation.partnerships.description')"
                :route="{ name: ROUTE_PARTNERSHIPS.name }"
              />

              <NavigationMenuSubLink
                :title="t('navigation.career.title')"
                :description="t('navigation.career.description')"
                :route="{ name: ROUTE_CAREER.name }"
              />
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
        :href="LINK.HelloAssoDonation"
        target="_blank"
      >
        <Button
          label="Faire un don"
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
import { SOCIAL_LINK } from '@lychen/typescript-constants/Social';
import { ROUTE_PARTNERSHIPS } from '@/views/partnerships';
import { ROUTE_CAREER } from '@/views/career';
import { ROUTE_CHARTER } from '@/views/charter';
import { ROUTE_TEAM } from '@/views/team';
import { ROUTE_MISSION } from '@/views/mission';
import { ROUTE_APPLICATIONS } from '@/views/applications';
import IconHeartHandshake from '@lychen/vue-icons/IconHeartHandshake.vue';

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
