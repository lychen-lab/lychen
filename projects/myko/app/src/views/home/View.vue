<template>
  <section class="flex flex-1 flex-col items-center justify-center gap-6 text-center">
    <img
      src="/icons/myko.svg"
      alt=""
      class="size-24"
    />
    <div class="flex flex-col gap-2">
      <h1 class="font-lexend text-4xl font-bold">{{ t('title') }}</h1>
      <p class="text-on-surface/70">{{ t('tagline') }}</p>
    </div>
    <div
      v-if="auth.isAuthenticated.value"
      class="flex flex-col items-center gap-3"
    >
      <p>{{ t('signed_in_as', { name: auth.userName.value ?? '' }) }}</p>
      <Button
        :label="t('sign_out')"
        variant="outline"
        @click="auth.signOut()"
      />
    </div>
  </section>
</template>

<script lang="ts" setup>
import { Button } from '@lychen/vue-components-core/button';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import { useDocumentTitle } from '@lychen/vue-myko/composables/use-document-title/useDocumentTitle';

import { useAuth } from '@/auth';

import { MESSAGES, TRANSLATION_KEY } from './i18n';

const { t } = useI18nExtended({ messages: MESSAGES, rootKey: TRANSLATION_KEY, prefixed: true });

const auth = useAuth();

useDocumentTitle(() => t('page_title'));
</script>
