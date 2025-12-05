<template>
  <div class="mt-4 flex w-full flex-col items-stretch justify-start gap-2">
    <Accordion
      type="single"
      class="w-full"
      collapsible
      default-value="initiatives"
    >
      <AccordionItem value="initiatives">
        <AccordionTrigger class="text-lg font-bold">{{
          t(`navigation.initiatives.title`)
        }}</AccordionTrigger>
        <AccordionContent>
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
        </AccordionContent>
      </AccordionItem>
    </Accordion>
    <RouterLink
      :to="i18nRoute({ name: ROUTE_MISSION.name })"
      class="text-lg font-bold"
    >
      <NavigationMenuLink as="div">
        {{ t(`navigation.mission.title`) }}
      </NavigationMenuLink>
    </RouterLink>
    <Accordion
      type="single"
      class="w-full"
      collapsible
      default-value="about"
    >
      <AccordionItem value="about">
        <AccordionTrigger class="text-lg font-bold">{{
          t(`navigation.about.title`)
        }}</AccordionTrigger>
        <AccordionContent>
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
        </AccordionContent>
      </AccordionItem>
    </Accordion>
    <a
      :href="LINK.Blog"
      target="_blank"
      rel="noopener noreferrer"
    >
      <NavigationMenuLink
        as="div"
        class="text-lg font-bold"
      >
        {{ t(`navigation.blog.title`) }}
      </NavigationMenuLink>
    </a>
  </div>
</template>

<script setup lang="ts">
import { CONFIG } from './i18n';
import { usePrefixedI18n } from '@lychen/vue-i18n/composables/useI18nExtended';
import NavigationMenuSubLink from '@lychen/vue-components-core/navigation-menu/NavigationMenuSubLink.vue';
import Accordion from '@lychen/vue-components-core/accordion/Accordion.vue';
import AccordionTrigger from '@lychen/vue-components-core/accordion/AccordionTrigger.vue';
import AccordionContent from '@lychen/vue-components-core/accordion/AccordionContent.vue';
import AccordionItem from '@lychen/vue-components-core/accordion/AccordionItem.vue';
import { inject, type Ref } from 'vue';
import { ROUTE_MISSION } from '@/views/mission';
import { ROUTE_APPLICATIONS } from '@/views/applications';
import { SOCIAL_LINK } from '@lychen/typescript-constants/Social';
import { ROUTE_PARTNERSHIPS } from '@/views/partnerships';
import { ROUTE_CAREER } from '@/views/career';
import { ROUTE_TEAM } from '@/views/team';
import { ROUTE_CHARTER } from '@/views/charter';
import { LINK } from '@lychen/typescript-constants/Link';

const { t, i18nRoute } = usePrefixedI18n(CONFIG);

const mobileMenuIsOpen = inject<Ref<boolean>>('mobileMenuIsOpen');

function closeMobileMenu() {
  mobileMenuIsOpen!.value = false;
}
</script>

<style lang="css" scoped></style>
