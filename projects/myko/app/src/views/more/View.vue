<template>
  <section class="flex flex-col gap-6">
    <h1 class="font-lexend text-2xl font-bold">{{ t('title') }}</h1>
    <ul class="flex flex-col divide-y divide-current/10 rounded-xl bg-surface-container">
      <li
        v-for="link in links"
        :key="link.name"
      >
        <RouterLink
          :to="{ name: link.name }"
          class="flex min-h-14 items-center justify-between gap-3 px-4"
        >
          <span>{{ link.label }}</span>
          <IconChevronRight class="size-5 opacity-60" />
        </RouterLink>
      </li>
    </ul>
    <div
      v-if="auth.isAuthenticated.value"
      class="flex flex-col items-start gap-3"
    >
      <p class="text-on-surface/70">{{ t('signed_in_as', { name: auth.userName.value ?? '' }) }}</p>
      <Button
        :label="t('sign_out')"
        variant="outline"
        @click="auth.signOut()"
      />
    </div>
  </section>
</template>

<script lang="ts" setup>
import { computed } from 'vue';

import { Button } from '@lychen/vue-components-core/button';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import IconChevronRight from '@lychen/vue-icons/IconChevronRight.vue';
import { useDocumentTitle } from '@lychen/vue-myko/composables/use-document-title/useDocumentTitle';

import { useAuth } from '@/auth';
import { ROUTE_NAME } from '@/router/routeNames';

import { MESSAGES, TRANSLATION_KEY } from './i18n';

const { t } = useI18nExtended({ messages: MESSAGES, rootKey: TRANSLATION_KEY, prefixed: true });

const auth = useAuth();

useDocumentTitle(() => t('title'));

const links = computed(() => [
  { name: ROUTE_NAME.Zones, label: t('zones') },
  { name: ROUTE_NAME.Strains, label: t('strains') },
  { name: ROUTE_NAME.Labels, label: t('labels') },
]);
</script>
