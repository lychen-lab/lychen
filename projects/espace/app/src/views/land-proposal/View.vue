<template>
  <section class="flex flex-col gap-4">
    <Button
      icon-only
      variant="ghost"
      size="sm"
      @click="$router.back()"
    >
      <template #icon>
        <IconArrowLeft />
      </template>
    </Button>
    <h2 class="text-xl font-bold">Espace TITLE</h2>
  </section>
</template>

<script lang="ts" setup>
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import { MESSAGES, TRANSLATION_KEY } from './i18n';
import { Button } from '@lychen/vue-components-core/button';
import IconArrowLeft from '@lychen/vue-icons/IconArrowLeft.vue';

const { t } = useI18nExtended({ messages: MESSAGES, rootKey: TRANSLATION_KEY, prefixed: true });
</script>

<style lang="css" scoped></style>
