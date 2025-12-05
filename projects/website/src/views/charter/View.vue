<template>
  <div class="flex flex-row gap-4">
    {{ t('title') }}
    - Charte avec explication pour chaque éléments
  </div>
</template>

<script lang="ts" setup>
import { usePrefixedI18n } from '@lychen/vue-i18n/composables/useI18nExtended';
import { CONFIG } from './i18n';

const { t } = usePrefixedI18n(CONFIG);
</script>
