<template>
  <Teleport
    defer
    to="#breadcrumb"
  >
    <BaseHeading
      v-if="land"
      variant="h3"
      >{{ land.name }}</BaseHeading
    >
  </Teleport>
  <RouterView />
</template>

<script lang="ts" setup>
import { RoutePageDashboard } from '@/pages/dashboard';
import { useTeraApi } from '@lychen/vue-tera/composables/use-tera-api/useTeraApi';
import { useQuery } from '@tanstack/vue-query';
import { computed, provide, watch, type Ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { INJECTION_KEY_LAND, INJECTION_KEY_LAND_MEMBER } from './index';
import type { components } from '@lychen/typescript-tera-api-sdk/generated/tera-api';
import { landPatchSucceededEvent } from '@lychen/vue-tera/events/LandEvents';
import { useEventBus } from '@vueuse/core';
import { BaseHeading } from '@lychen/vue-components-app/base-heading';

const route = useRoute();
const router = useRouter();
const { api } = useTeraApi();

const { data: land, refetch } = useQuery({
  queryKey: ['land'],
  queryFn: async () => {
    try {
      const response = await api.GET('/api/lands/{ulid}', {
        params: { path: { ulid: route.params.landUlid as string } },
      });
      return response.data;
    } catch {
      router.push(RoutePageDashboard);
      return Promise.reject(new Error('Failed to fetch land data'));
    }
  },
  enabled: !!route.params.landUlid,
});

// The `/api/lands/{ulid}` endpoint returns the `Land.jsonld-land.get` serialization
// group, which differs from the base `Land.jsonld` only by a generated `@context`
// enum that is never read by consumers. Cast at the provide boundary so downstream
// components receive the broader `Land.jsonld` shape they expect.
provide(
  INJECTION_KEY_LAND,
  land as unknown as Ref<components['schemas']['Land.jsonld'] | undefined>,
);

const { on } = useEventBus(landPatchSucceededEvent);

on(() => {
  refetch();
});

watch(
  () => route.params.landUlid,
  () => {
    refetch();
  },
);

const landUlid = computed(() => land?.value?.ulid);
const enabled = computed(() => !!landUlid.value);

const { data: landMember } = useQuery({
  queryKey: ['landMember', landUlid],
  queryFn: async () => {
    if (!landUlid.value) {
      throw new Error('missing.ulid');
    }
    const response = await api.GET('/api/land_members/me', {
      params: { query: { land: landUlid.value } },
    });
    return response.data;
  },
  enabled,
});

provide(INJECTION_KEY_LAND_MEMBER, landMember);
</script>

<style lang="css" scoped></style>
