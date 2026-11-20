<template>
  <p
    v-if="isPending"
    class="py-8 text-center text-sm text-on-surface/60"
    role="status"
  >
    {{ t('state.loading') }}
  </p>
  <div
    v-else-if="isError"
    class="flex flex-col items-center gap-3 py-8 text-center"
    role="alert"
  >
    <p class="text-sm text-negative">{{ errorText ?? t('state.error') }}</p>
    <Button
      variant="outline"
      size="sm"
      @click="emit('retry')"
    >
      {{ t('state.retry') }}
    </Button>
  </div>
  <p
    v-else-if="isEmpty"
    class="py-8 text-center text-sm text-on-surface/60"
  >
    {{ emptyText }}
  </p>
  <slot v-else />
</template>

<script lang="ts" setup>
import Button from '@lychen/vue-components-core/button/Button.vue';
import { useAppI18n } from '@/i18n';

/** The loading, error and empty states every screen reading the API goes through. */
defineProps<{
  isPending: boolean;
  isError: boolean;
  isEmpty?: boolean;
  emptyText?: string;
  errorText?: string;
}>();

const emit = defineEmits<{
  (e: 'retry'): void;
}>();

const { t } = useAppI18n();
</script>
