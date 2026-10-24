<template>
  <Badge
    :class="classes[state]"
    class="uppercase"
  >
    <IconCircleDashed />
    {{ t(state) }}
  </Badge>
</template>

<script lang="ts" setup>
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import { messages, TRANSLATION_KEY } from './i18n';

import Badge from '@lychen/vue-components-core/badge/Badge.vue';
import IconCircleDashed from '@lychen/vue-icons/IconCircleDashed.vue';

import { LandTaskState as State } from '@lychen/typescript-tera-api-sdk/enums';

const { t } = useI18nExtended({ messages, rootKey: TRANSLATION_KEY, prefixed: true });
defineProps<{ state: State }>();

const classes = {
  [State.to_be_done]: 'bg-surface-container-high text-on-surface-container-high',
  [State.in_progress]: 'bg-secondary text-on-secondary',
  [State.done]: 'bg-positive text-on-positive',
};
</script>
