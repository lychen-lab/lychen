<template>
  <div class="mt-4 flex w-full flex-col items-stretch justify-start gap-2">
    <ButtonTallyPreregister />
    <div class="text-lg font-bold">
      <RouterLink
        :to="{ name: ROUTE_HOME.name }"
        @click="closeMobileMenu"
        >{{ t(`navigation.home.title`) }}
      </RouterLink>
    </div>
    <Accordion
      type="single"
      class="w-full"
      collapsible
      :default-value="'resources'"
    >
      <AccordionItem value="applications">
        <AccordionTrigger class="text-lg font-bold">{{
          t(`navigation.app.title`)
        }}</AccordionTrigger>
        <AccordionContent>
          <NavigationMenuSubLink
            v-for="application in opiniatedApplicationsList"
            v-bind="application"
            :key="application.title"
            @navigate-to-route="closeMobileMenu"
          />
          <NavigationMenuSubLink
            v-bind="robust"
            @navigate-to-route="closeMobileMenu"
          />
        </AccordionContent>
      </AccordionItem>
      <AccordionItem value="resources">
        <AccordionTrigger class="text-lg font-bold">{{
          t(`navigation.resources.title`)
        }}</AccordionTrigger>
        <AccordionContent>
          <NavigationMenuSubLink
            v-for="resourceMenu in resourcesMenuList"
            v-bind="resourceMenu"
            :key="resourceMenu.title"
            @navigate-to-route="closeMobileMenu"
          />
        </AccordionContent>
      </AccordionItem>
      <AccordionItem value="community">
        <AccordionTrigger class="text-lg font-bold">{{
          t(`navigation.community.title`)
        }}</AccordionTrigger>
        <AccordionContent>
          <NavigationMenuSubLink
            v-for="communityMenu in communityMenuList"
            v-bind="communityMenu"
            :key="communityMenu.title"
            @navigate-to-route="closeMobileMenu"
          />
        </AccordionContent>
      </AccordionItem>
    </Accordion>
  </div>
</template>

<script setup lang="ts">
import { useApplicationsCatalog } from '@lychen/vue-applications/composables/useApplicationsCatalog';
import { APPLICATION_ALIAS } from '@lychen/typescript-applications/constants/ApplicationAlias';

import { messages, TRANSLATION_KEY } from './i18n';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import { ROUTE_HOME } from '@/views/home';
import { useCommunityMenu } from './composables/useCommunityMenu';
import { useResourcesMenu } from './composables/useResourcesMenu';
import NavigationMenuSubLink from '@lychen/vue-components-core/navigation-menu/NavigationMenuSubLink.vue';
import Accordion from '@lychen/vue-components-core/accordion/Accordion.vue';
import AccordionTrigger from '@lychen/vue-components-core/accordion/AccordionTrigger.vue';
import AccordionContent from '@lychen/vue-components-core/accordion/AccordionContent.vue';
import AccordionItem from '@lychen/vue-components-core/accordion/AccordionItem.vue';
import { inject, type Ref, computed } from 'vue';
import ButtonTallyPreregister from '@lychen/vue-components-website/button-tally-preregister/ButtonTallyPreregister.vue';

const { t } = useI18nExtended({ messages, rootKey: TRANSLATION_KEY, prefixed: true });

const { opiniatedApplicationsList, getAppInfo } = useApplicationsCatalog();
const { communityMenuList } = useCommunityMenu();
const { resourcesMenuList } = useResourcesMenu();
const robust = computed(() => getAppInfo(APPLICATION_ALIAS.Robust));
const mobileMenuIsOpen = inject<Ref<boolean>>('mobileMenuIsOpen');

function closeMobileMenu() {
  mobileMenuIsOpen!.value = false;
}
</script>

<style lang="css" scoped></style>
