<template>
  <Badge
    variant="tertiary"
    class="self-center"
    >{{ t(`state.${state}`) }}</Badge
  >
</template>

<script setup lang="ts">
import { usePrefixedI18n } from '@lychen/vue-i18n/composables/useI18nExtended';
import { CONFIG } from '@lychen/vue-applications/i18n';
import { type ApplicationState } from '@lychen/typescript-applications/constants/ApplicationState';
import { APPLICATION_STATE } from '@lychen/typescript-applications/constants/ApplicationState';
import Badge from '@lychen/vue-components-core/badge/Badge.vue';

const { t } = usePrefixedI18n(CONFIG);

const { state = APPLICATION_STATE.Development } = defineProps<{ state?: ApplicationState }>();
</script>
