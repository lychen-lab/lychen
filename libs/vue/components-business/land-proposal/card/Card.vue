<template>
  <div
    class="bg-surface-container max-w-[300px] rounded-xl p-4"
    :data-uuid="uuid"
  >
    <h3 class="font-bold">{{ name }}</h3>
    <p class="opacity-70">{{ description }}</p>
    <div>
      <Badge>{{ n(surface, 'square-meter') }}</Badge>
      <Badge class="flex items-center"
        ><IconMountain class="text-teal-700" />{{ n(altitude, 'meter') }}</Badge
      >
    </div>
  </div>
</template>

<script setup lang="ts">
import type { LandProposal } from '.';
import { Badge } from '@lychen/vue-components-core/badge';
import IconMountain from '@lychen/vue-icons/IconMountain.vue';
import { usePrefixedI18n } from '@lychen/vue-i18n/composables/useI18nExtended';

interface Props extends LandProposal {}

const { n } = usePrefixedI18n();
defineProps<Props>();
</script>
