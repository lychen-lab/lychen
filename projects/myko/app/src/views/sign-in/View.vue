<template>
  <main class="flex min-h-dvh flex-col items-center justify-center gap-6 p-4 text-center">
    <img
      src="/icons/myko.svg"
      alt=""
      class="size-24"
    />
    <div class="flex flex-col gap-2">
      <h1 class="font-lexend text-4xl font-bold">{{ t('title') }}</h1>
      <p class="text-on-surface/70">{{ t('tagline') }}</p>
    </div>
    <Button
      v-if="auth.isConfigured"
      :label="t('sign_in')"
      @click="auth.signIn()"
    />
    <p
      v-else
      role="status"
      class="text-on-surface/60"
    >
      {{ t('not_configured') }}
    </p>
  </main>
</template>

<script lang="ts" setup>
import { watchEffect } from 'vue';
import { useRoute, useRouter } from 'vue-router';

import { Button } from '@lychen/vue-components-core/button';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import { useDocumentTitle } from '@lychen/vue-myko/composables/use-document-title/useDocumentTitle';

import { getRedirectTarget, useAuth } from '@/auth';

import { MESSAGES, TRANSLATION_KEY } from './i18n';

const { t } = useI18nExtended({ messages: MESSAGES, rootKey: TRANSLATION_KEY, prefixed: true });

const auth = useAuth();
const route = useRoute();
const router = useRouter();

useDocumentTitle(() => t('page_title'));

// Signed in, now or already: back to where the visitor was going.
watchEffect(() => {
  if (auth.isAuthenticated.value) {
    void router.replace(getRedirectTarget(route.query.redirect));
  }
});
</script>
