<template>
  <section class="flex flex-1 flex-col items-center justify-center gap-2 text-center">
    <h1 class="font-lexend text-2xl font-bold">{{ title }}</h1>
    <p class="text-on-surface/70">{{ t('coming_soon') }}</p>
  </section>
</template>

<script lang="ts" setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';

import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import { useDocumentTitle } from '@lychen/vue-myko/composables/use-document-title/useDocumentTitle';

import { MESSAGES, TRANSLATION_KEY } from './i18n';

const { t } = useI18nExtended({ messages: MESSAGES, rootKey: TRANSLATION_KEY, prefixed: true });

const route = useRoute();

const title = computed(() => t(`titles.${String(route.name)}`));

useDocumentTitle(title);
</script>
