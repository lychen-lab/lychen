<template>
  <section class="flex h-full flex-col items-stretch">
    <SectionDevelopmentInProgress title="Itinéraires de culture" />
  </section>
</template>

<script lang="ts" setup>
import { INJECTION_KEY_LAND } from '@/layouts/land-layout';
import { useTeraApi } from '@lychen/vue-tera/composables/use-tera-api/useTeraApi';
import SectionDevelopmentInProgress from '@lychen/vue-components-app/section-development-in-progress/SectionDevelopmentInProgress.vue';
import { useQuery } from '@tanstack/vue-query';
import { computed, inject } from 'vue';

const land = inject(INJECTION_KEY_LAND);

const landUlid = computed(() => land?.value?.ulid);
const enabled = computed(() => !!landUlid.value);

const { api } = useTeraApi();

const { data: landCultivationPlans } = useQuery({
  queryKey: ['landCultivationPlans', landUlid],
  queryFn: async () => {
    const response = await api.GET('/api/land_cultivation_plans', {
      params: {
        query: {
          land: landUlid.value!,
        },
      },
    });

    return response.data;
  },
  enabled,
});
</script>

<style lang="css" scoped></style>
