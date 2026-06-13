<template>
  <section
    v-if="land"
    class="flex flex-col gap-6"
  >
    <div class="flex flex-row items-center justify-between gap-4">
      <BaseHeading>{{ land.name }}</BaseHeading>
      <div class="flex flex-row gap-2">
        <DialogTeraLandMemberDelete
          v-if="landMemberForDelete && !landMemberForDelete.owner"
          :land-member="landMemberForDelete"
          leave
        >
          <Button variant="ghost">
            <template #icon>
              <IconRightFromBracket />
            </template>
          </Button>
        </DialogTeraLandMemberDelete>
        <RouterLink
          :to="{ name: RoutePageLandMemberSettings.name, params: { landUlid: land.ulid } }"
        >
          <Button variant="ghost">
            <template #icon>
              <IconUserGear />
            </template>
          </Button>
        </RouterLink>
        <RouterLink
          v-if="settingsButtonAllowed"
          :to="{ name: RoutePageLandSettings.name, params: { landUlid: land.ulid } }"
        >
          <Button variant="ghost">
            <template #icon>
              <IconGear />
            </template>
          </Button>
        </RouterLink>
      </div>
    </div>

    <div class="dashboard-grid grid grid-cols-[1fr_30%] grid-rows-2 gap-8">
      <div
        id="header"
        class="border-surface-container/100 flex flex-row items-center justify-between rounded-xl border-1 p-4"
      >
        <div class="flex flex-row items-center gap-4">
          <BaseHeading variant="h4">Actions rapide</BaseHeading>
          <RouterLink :to="RoutePageLandSettings">
            <Button
              label="Inviter"
              size="sm"
              variant="outline"
            >
              <template #icon>
                <IconUserPlus />
              </template>
            </Button>
          </RouterLink>
        </div>
        <div class="flex flex-row gap-2">
          <Button
            label="Planifier une culture"
            variant="outline"
            size="sm"
            disabled
          >
            <template #icon>
              <IconCalendarCirclePlus />
            </template>
          </Button>
          <Button
            label="Ajouter une tâche"
            size="sm"
            variant="outline"
          >
            <template #icon>
              <IconTasks />
            </template>
          </Button>
          <Button
            label="Prendre une note"
            variant="outline"
            size="sm"
            disabled
          >
          </Button>
        </div>
        <div>
          <Button
            label="Enregistrer une mesure"
            disabled
            size="sm"
            variant="outline"
          >
          </Button>
        </div>
      </div>
      <div id="principal">
        <div class="bg-surface-container flex h-full flex-col justify-between gap-4 rounded-xl p-8">
          <BaseHeading>Tâches en cours</BaseHeading>
          <div class="flex flex-row gap-4 self-end">
            <Button
              label="Voir les calendriers"
              size="sm"
              variant="ghost"
              class="self-end"
              disabled
            />
            <RouterLink :to="RoutePageLandTasks">
              <Button
                label="Voir toutes les tâches"
                size="sm"
                variant="ghost"
              />
            </RouterLink>
          </div>
        </div>
      </div>

      <div
        id="side"
        class="flex flex-col gap-8"
      >
        <Card class="gap-4 bg-gradient-to-tr from-purple-500 to-pink-500">
          <div class="flex flex-col gap-0">
            <BaseHeading variant="h3">Un surplus ?</BaseHeading>
            <p>Signalez le on s'occupe de trouver quelqu'un pour que ce ne soit pas perdu.</p>
          </div>
          <BadgeDevelopmentInProgress class="self-start" />
          <Button
            label="Signaler un surplus"
            class="self-end bg-purple-200 text-black hover:bg-purple-700 hover:text-purple-200"
            disabled
          >
          </Button>
        </Card>
        <Card class="gap-4 bg-gradient-to-tr from-amber-500 to-yellow-500">
          <div class="flex flex-col gap-0 text-amber-800">
            <BaseHeading
              variant="h3"
              class="text-amber-800"
              >Pollinisateurs</BaseHeading
            >
            <p>Voulez-vous accueillir des pollinisateurs ?</p>
          </div>
          <BadgeDevelopmentInProgress class="self-start" />
          <Button
            label="Demander"
            class="self-end bg-amber-200 text-black hover:bg-amber-700 hover:text-amber-200"
            disabled
          >
          </Button>
        </Card>
        <BannerTeraShareYourLand />
      </div>
    </div>

    <div class="flex flex-col gap-4">
      <div class="flex flex-row items-center gap-4">
        <BaseHeading variant="h3">Zones de culture</BaseHeading>
        <div class="flex flex-row gap-2">
          <Button variant="ghost">
            <template #icon>
              <IconPlus />
            </template>
          </Button>
          <Button variant="ghost">
            <template #icon>
              <IconList />
            </template>
          </Button>
        </div>
      </div>

      <Carousel
        v-if="landAreas"
        :opts="{
          align: 'start',
        }"
      >
        <CarouselContent>
          <CarouselItem
            v-for="(item, index) in landAreas.member"
            :key="index"
            class="h-[100px] basis-3/5 md:basis-1/2 lg:basis-1/10"
          >
            <CardTeraLandArea
              :land-area="asLandArea(item)"
              hoverable
            />
          </CarouselItem>
        </CarouselContent>
      </Carousel>
    </div>

    <div class="flex flex-col gap-4">
      <div class="flex flex-row items-center justify-between">
        <Title variant="h4">Serres</Title>
        <div class="flex flex-row gap-2">
          <Button variant="ghost">
            <template #icon>
              <IconPlus />
            </template>
          </Button>
          <Button variant="ghost">
            <template #icon>
              <IconList />
            </template>
          </Button>
        </div>
      </div>

      <Carousel
        v-if="landGreenhouses"
        :opts="{
          align: 'start',
        }"
      >
        <CarouselContent>
          <CarouselItem
            v-for="(item, index) in landGreenhouses.member"
            :key="index"
            class="h-[200px] basis-3/5 md:basis-1/2 lg:basis-1/8"
          >
            <CardTeraLandGreenhouse :land-greenhouse="item" />
          </CarouselItem>
        </CarouselContent>
      </Carousel>
    </div>
  </section>
</template>

<script lang="ts" setup>
import { defineAsyncComponent, computed, inject } from 'vue';
import CardTeraLandGreenhouse from '@lychen/vue-tera/components/land-greenhouse/card/CardTeraLandGreenhouse.vue';
import CardTeraLandArea from '@lychen/vue-tera/components/land-area/card/CardTeraLandArea.vue';
import { useTeraApi } from '@lychen/vue-tera/composables/use-tera-api/useTeraApi';
import { useQuery } from '@tanstack/vue-query';
import Carousel from '@lychen/vue-components-core/carousel/Carousel.vue';
import CarouselItem from '@lychen/vue-components-core/carousel/CarouselItem.vue';
import CarouselContent from '@lychen/vue-components-core/carousel/CarouselContent.vue';
import { INJECTION_KEY_LAND, INJECTION_KEY_LAND_MEMBER } from '@/layouts/land-layout';
import { BaseHeading } from '@lychen/vue-components-app/base-heading';
import DialogTeraLandMemberDelete from '@lychen/vue-tera/components/land-member/dialogs/delete/DialogTeraLandMemberDelete.vue';
import { RoutePageLandSettings } from '../settings';
import { useLandGuard } from '@lychen/vue-tera/composables/use-land-guard';
import { RoutePageLandMemberSettings } from '../member-settings';
import { landMemberLeaveSucceededEvent } from '@lychen/vue-tera/events/LandMemberEvents';
import { RoutePageDashboard } from '@/pages/dashboard';
import { useEventBus } from '@vueuse/core';
import { useRouter } from 'vue-router';
import Card from '@lychen/vue-components-core/card/Card.vue';
import BadgeDevelopmentInProgress from '@lychen/vue-components-app/badge-development-in-progress/BadgeDevelopmentInProgress.vue';
import { RoutePageLandTasks } from '../tasks';
import BannerTeraShareYourLand from '@/components/banners/BannerTeraShareYourLand.vue';
import IconUserGear from '@lychen/vue-icons/IconUserGear.vue';
import IconGear from '@lychen/vue-icons/IconGear.vue';
import IconPlus from '@lychen/vue-icons/IconPlus.vue';
import IconList from '@lychen/vue-icons/IconList.vue';
import IconUserPlus from '@lychen/vue-icons/IconUserPlus.vue';
import IconCalendarCirclePlus from '@lychen/vue-icons/IconCalendarCirclePlus.vue';
import IconTasks from '@lychen/vue-icons/IconTasks.vue';
import IconRightFromBracket from '@lychen/vue-icons/IconRightFromBracket.vue';
import type { components } from '@lychen/typescript-tera-api-sdk/generated/tera-api';

const Title = defineAsyncComponent(() => import('@lychen/vue-components-website/title/Title.vue'));

const Button = defineAsyncComponent(() => import('@lychen/vue-components-core/button/Button.vue'));

const { api } = useTeraApi();

const land = inject(INJECTION_KEY_LAND);
const landMember = inject(INJECTION_KEY_LAND_MEMBER);

const { allowed: settingsButtonAllowed } = useLandGuard(landMember, ['land_update']);

// `DialogTeraLandMemberDelete` expects the broader `LandMember.jsonld` shape, which
// differs from the injected `land_member.me` group only by a generated `@context`
// enum that is never read. Cast for the delete dialog binding.
const landMemberForDelete = computed(
  () =>
    landMember?.value as unknown as
      | Omit<components['schemas']['LandMember.jsonld'], 'landRoles'>
      | undefined,
);

const landUlid = computed(() => land?.value?.ulid);
const enabled = computed(() => !!landUlid.value);

// The `/api/land_areas` query returns the leaner `land_area.collection` projection, while
// CardTeraLandArea is typed against the base `LandArea.jsonld`. The projection is a runtime
// subset (it only omits optional base fields such as `@context` and uses nominally distinct
// but value-identical enums), so it is safe to widen at this prop boundary.
function asLandArea(landArea: components['schemas']['LandArea.jsonld-land_area.collection']) {
  return landArea as unknown as components['schemas']['LandArea.jsonld'];
}

const { data: landAreas } = useQuery({
  queryKey: ['landAreas', landUlid],
  queryFn: async () => {
    const response = await api.GET('/api/land_areas', {
      params: { query: { land: landUlid.value! } },
    });

    return response.data;
  },
  enabled,
});

const { data: landGreenhouses } = useQuery({
  queryKey: ['landGreenhouses', landUlid],
  queryFn: async () => {
    const response = await api.GET('/api/land_greenhouses', {
      params: { query: { land: landUlid.value! } },
    });

    return response.data;
  },
  enabled,
});

const router = useRouter();
const { on } = useEventBus(landMemberLeaveSucceededEvent);

on(() => {
  router.push(RoutePageDashboard);
});
</script>

<style lang="css" scoped>
.dashboard-grid {
  grid-template-areas: 'header header' 'principal side';
  grid-template-rows: auto;

  #header {
    grid-area: header;
  }

  #principal {
    grid-area: principal;
  }

  #side {
    grid-area: side;
  }
}
</style>
