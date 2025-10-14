<template>
  <LayoutInAppRoot>
    <LayoutInAppSideNavigation
      v-slot="{ expanded }"
      class="flex flex-col justify-between"
    >
      <LayoutInAppSideNavigationButtonExpand />
      <LayoutInAppSideNavigationHeaderApp name="tera" />
      <div class="flex flex-col gap-4">
        <LayoutInAppSideNavigationSection>
          <template #header>
            <div :class="expanded ? 'flex flex-row justify-between gap-2 items-center' : ''">
              <LayoutInAppSideNavigationSectionLabel :label="landSection.label" />
              <div
                v-if="expanded"
                class="flex flex-row items-center justify-end gap-2"
              >
                <RouterLink :to="RoutePageLands">
                  <Button
                    variant="ghost"
                    size="xs"
                  >
                    <template #icon>
                      <IconList />
                    </template>
                  </Button>
                </RouterLink>
                <DialogTeraLandCreate v-model:open="open">
                  <Button
                    variant="ghost"
                    size="xs"
                  >
                    <template #icon>
                      <IconPlus />
                    </template>
                  </Button>
                </DialogTeraLandCreate>
              </div>
            </div>
          </template>
          <Select
            v-if="lands && lands.member && expanded"
            v-model="selectedLand"
          >
            <SelectTrigger class="w-auto">
              <SelectValue placeholder="Select a land" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem
                v-for="(land, index) in lands.member"
                :key="index"
                :value="land"
              >
                {{ land.name }}
              </SelectItem>
            </SelectContent>
          </Select>
          <template v-if="selectedLand">
            <LayoutInAppSideNavigationItem
              v-for="(item, indexItems) in landSection.items"
              :key="indexItems"
              :to="{ name: item.to.name, params: { landUlid: selectedLand.ulid } }"
              :icon="item.icon"
              :label="item.label"
          /></template>
        </LayoutInAppSideNavigationSection>
        <LayoutInAppSideNavigationSection
          v-for="(section, indexSections) in navigation"
          :key="indexSections"
          :label="section.label"
        >
          <template v-if="section.items">
            <LayoutInAppSideNavigationItem
              v-for="(item, indexItems) in section.items"
              :key="indexItems"
              v-bind="item"
            />
          </template>
        </LayoutInAppSideNavigationSection>
      </div>
      <LayoutInAppSideNavigationFooter />
    </LayoutInAppSideNavigation>
    <LayoutInAppContent />
    <LayoutInAppHeader class="justify-between gap-2">
      <div id="breadcrumb"></div>
      <div class="flex flex-row items-center justify-end gap-2">
        <AppMenu />
        <UserAvatar
          :first-name="zitadelAuth.oidcAuth.userProfile.given_name"
          :last-name="zitadelAuth.oidcAuth.userProfile.family_name"
          :email="zitadelAuth.oidcAuth.userProfile.email"
        />
      </div>
    </LayoutInAppHeader>
  </LayoutInAppRoot>
</template>

<script setup lang="ts">
import Button from '@lychen/vue-components-core/button/Button.vue';
import IconPlus from '@lychen/vue-icons/IconPlus.vue';
import IconList from '@lychen/vue-icons/IconList.vue';
import { useTeraApi } from '@lychen/vue-tera/composables/use-tera-api/useTeraApi';
import { useQuery } from '@tanstack/vue-query';
import { useEventBus } from '@vueuse/core';
import {
  landDeleteSucceededEvent,
  landPostSucceededEvent,
  landPatchSucceededEvent,
} from '@lychen/vue-tera/events/LandEvents';
import { landMemberInvitationAcceptSucceededEvent } from '@lychen/vue-tera/events/LandMemberInvitationEvents';
import DialogTeraLandCreate from '@lychen/vue-tera/components/land/dialogs/create/DialogTeraLandCreate.vue';
import { ref } from 'vue';
import { landMemberLeaveSucceededEvent } from '@lychen/vue-tera/events/LandMemberEvents';
import {
  LayoutInAppContent,
  LayoutInAppHeader,
  LayoutInAppRoot,
  LayoutInAppSideNavigation,
  LayoutInAppSideNavigationButtonExpand,
  LayoutInAppSideNavigationFooter,
  LayoutInAppSideNavigationHeaderApp,
  LayoutInAppSideNavigationItem,
  LayoutInAppSideNavigationSection,
} from '@lychen/vue-layouts/in-app';
import useMenus from '@/layouts/main/useMenus';
import { RoutePageLands } from '@/pages/lands';
import LayoutInAppSideNavigationSectionLabel from '@lychen/vue-layouts/in-app/LayoutInAppSideNavigationSectionLabel.vue';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@lychen/vue-components-core/select';
import AppMenu from '@lychen/vue-components-app/app-menu/AppMenu.vue';
import UserAvatar from '@lychen/vue-components-app/user-avatar/UserAvatar.vue';
import zitadelAuth from '@lychen/typescript-zitadel/ZitadelAuth';

const open = ref(false);

const { menus: navigation, landSection } = useMenus();
const selectedLand = ref<{ ulid?: string }>();

const { api } = useTeraApi();

const { data: lands, refetch } = useQuery({
  queryKey: ['lands'],
  queryFn: async () => {
    const response = await api.GET('/api/lands');
    selectedLand.value = response.data?.member[0];
    return response.data;
  },
});

const { on: onLandPost } = useEventBus(landPostSucceededEvent);

onLandPost(() => {
  refetch();
  open.value = false;
});

const { on: onLandDelete } = useEventBus(landDeleteSucceededEvent);

onLandDelete(() => {
  refetch();
});

const { on: onLandUpdate } = useEventBus(landPatchSucceededEvent);

onLandUpdate(() => {
  refetch();
});

const { on: onLandMemberInvitationAccept } = useEventBus(landMemberInvitationAcceptSucceededEvent);

onLandMemberInvitationAccept(() => {
  refetch();
});

const { on: onLeaveLand } = useEventBus(landMemberLeaveSucceededEvent);

onLeaveLand(() => {
  refetch();
});
</script>
